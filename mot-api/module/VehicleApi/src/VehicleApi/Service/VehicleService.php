<?php

namespace VehicleApi\Service;

use DataCatalogApi\Service\VehicleCatalogService;
use Doctrine\ORM\EntityRepository;
use DvsaAuthorisation\Service\AuthorisationServiceInterface;
use DvsaCommon\Auth\PermissionInSystem;
use DvsaCommon\Date\DateUtils;
use DvsaCommon\Enum\WeightSourceCode;
use DvsaCommon\Utility\ArrayUtils;
use DvsaEntities\Entity\DvlaVehicle;
use DvsaEntities\Entity\DvlaVehicleImportChangeLog;
use DvsaEntities\Entity\Person;
use DvsaEntities\Entity\Vehicle;
use DvsaEntities\Entity\VehicleV5C;
use DvsaEntities\Repository\DvlaMakeModelMapRepository;
use DvsaEntities\Repository\DvlaVehicleImportChangesRepository;
use DvsaEntities\Repository\DvlaVehicleRepository;
use DvsaEntities\Repository\VehicleRepository;
use DvsaEntities\Repository\VehicleV5CRepository;
use DvsaMotApi\Service\OtpService;
use DvsaMotApi\Service\Validator\VehicleValidator;
use VehicleApi\Service\Mapper\DvlaVehicleMapper;
use VehicleApi\Service\Mapper\VehicleMapper;

/**
 * Class VehicleService.
 */
class VehicleService
{
    const DEFAULT_COUNTRY_OF_REGISTRATION = 'GB';
    const DEFAULT_MAKE_MODEL_NAME = 'Unknown';
    /**
     * Uplift added to unladen weight in kilos.
     */
    const UNLADEN_WEIGHT_UPLIFT = 140;

    /** @var  AuthorisationServiceInterface */
    private $authService;
    /** @var  VehicleRepository */
    private $vehicleRepository;
    /** @var  DvlaVehicleRepository */
    private $dvlaVehicleRepository;
    /** @var DvlaVehicleImportChangesRepository */
    private $dvlaVehicleImportChangesRepository;
    /** @var EntityRepository */
    private $dvlaMakeModelMapRepository;
    /** @var VehicleV5CRepository */
    private $vehicleV5CRepository;
    /** @var VehicleCatalogService */
    private $vehicleCatalog;
    /** @var  VehicleValidator */
    private $validator;
    /** @var  OtpService */
    private $otpService;

    public function __construct(
        AuthorisationServiceInterface $authService,
        VehicleRepository $repository,
        VehicleV5CRepository $vehicleV5CRepository,
        DvlaVehicleRepository $dvlaVehicleRepository,
        DvlaVehicleImportChangesRepository $dvlaVehicleImportChangesRepository,
        EntityRepository $dvlaMakeModelMapRepository,
        VehicleCatalogService $vehicleCatalog,
        VehicleValidator $validator,
        OtpService $otpService
    ) {
        $this->authService = $authService;
        $this->vehicleRepository = $repository;
        $this->vehicleV5CRepository = $vehicleV5CRepository;
        $this->dvlaVehicleRepository = $dvlaVehicleRepository;
        $this->dvlaVehicleImportChangesRepository =
            $dvlaVehicleImportChangesRepository;
        $this->dvlaMakeModelMapRepository = $dvlaMakeModelMapRepository;
        $this->vehicleCatalog = $vehicleCatalog;
        $this->validator = $validator;
        $this->otpService = $otpService;
    }

    public function create($data)
    {
        $this->authService->assertGranted(PermissionInSystem::VEHICLE_CREATE);
        $this->validator->validate($data);

        if (!$this->authService->isGranted(PermissionInSystem::MOT_TEST_WITHOUT_OTP)) {
            $token = ArrayUtils::tryGet($data, 'oneTimePassword');
            $this->otpService->authenticate($token);
        }

        $vehicle = $this->mapVehicle($data);
        $this->vehicleRepository->save($vehicle);

        return $vehicle->getId();
    }

    public function getVehicle($id)
    {
        $this->authService->assertGranted(PermissionInSystem::VEHICLE_READ);

        return $this->vehicleRepository->get($id);
    }

    private function getDvlaVehicle($id)
    {
        $this->authService->assertGranted(PermissionInSystem::VEHICLE_READ);

        return $this->dvlaVehicleRepository->get($id);
    }

    /**
     * @param $vehicleId
     *
     * @return \DvsaCommon\Dto\Vehicle\VehicleDto
     */
    public function getVehicleDto($vehicleId)
    {
        $v = $this->getVehicle($vehicleId);
        $m = (new VehicleMapper())->toDto($v)
            ->setId($vehicleId);

        return $m;
    }

    public function getDvlaVehicleData($vehicleId)
    {
        $dvlaVehicleMapper = new DvlaVehicleMapper($this->vehicleCatalog);

        return $dvlaVehicleMapper->toDto($this->getDvlaVehicle($vehicleId))
            ->setId($vehicleId);
    }

    /**
     * @param $dvlaVehicleId
     * @param $vehicleClassCode
     *
     * @return Vehicle
     */
    public function createVtrAndV5CFromDvlaVehicle($dvlaVehicleId, $vehicleClassCode)
    {
        $this->authService->assertGranted(PermissionInSystem::VEHICLE_READ);
        $this->authService->assertGranted(PermissionInSystem::VEHICLE_CREATE);

        $dvlaVehicle = $this->dvlaVehicleRepository->get($dvlaVehicleId);

        $fuelType = $this->vehicleCatalog->findFuelTypeByPropulsionCode($dvlaVehicle->getFuelType());
        $makeCode = $dvlaVehicle->getMakeCode();
        $modelCode = $dvlaVehicle->getModelCode();

        // Logic
        $makeModelName = self::DEFAULT_MAKE_MODEL_NAME;

        $legacyMakeName  = $this->vehicleCatalog->getMakeNameByDvlaCode($makeCode);
        $legacyModelName = $this->vehicleCatalog->getModelNameByDvlaCode($makeCode, $modelCode);

        if (!$dvlaVehicle->getMakeInFull()) {
            if (!is_null($makeCode) || !is_null($modelCode)) {
                $map = $this->vehicleCatalog->getMakeModelMapByDvlaCode($makeCode, $modelCode);

                if ($map) {
                    $makeName = (!$map->getMake()) ? self::DEFAULT_MAKE_MODEL_NAME : $map->getMake()->getName();
                    $modelName = (!$map->getModel()) ? self::DEFAULT_MAKE_MODEL_NAME : $map->getModel()->getName();
                } else {
                    $makeName  = $legacyMakeName;
                    $modelName = $legacyModelName;
                }

                $makeModelName = $makeName . ' ' . $modelName;

                if (!$makeName && !$modelName) {
                    $makeModelName = self::DEFAULT_MAKE_MODEL_NAME;
                }
            }
        } else {
            if (!$legacyMakeName && !$legacyModelName) {
                $makeModelName = $dvlaVehicle->getMakeInFull();
            } else {
                $makeModelName = $legacyMakeName . ' ' . $legacyModelName;
            }
        }

        // Search in DVSA make and model tables if no mapping is found
        $fallbackToDvsa = true;
        $map = $this->vehicleCatalog->getMakeModelMapByDvlaCode($makeCode, $modelCode, $fallbackToDvsa);
        $make = $map ? $map->getMake() : null;
        $model = $map ? $map->getModel() : null;
        $bodyType = $this->vehicleCatalog->findBodyTypeByCode($dvlaVehicle->getBodyType());

        $vehicle = (new Vehicle())
            ->setVin($dvlaVehicle->getVin())
            ->setRegistration($dvlaVehicle->getRegistration())
            ->setManufactureDate($dvlaVehicle->getManufactureDate())
            ->setFirstRegistrationDate($dvlaVehicle->getFirstRegistrationDate())
            ->setFirstUsedDate($dvlaVehicle->getFirstUsedDate())
            ->setColour($this->vehicleCatalog->getColourByCode($dvlaVehicle->getPrimaryColour()))
            ->setBodyType($bodyType)
            ->setCylinderCapacity($dvlaVehicle->getCylinderCapacity())
            ->setVehicleClass($this->vehicleCatalog->getVehicleClassByCode($vehicleClassCode))
            ->setMake($make)
            ->setModel($model)
            ->setMake($make)
            ->setCountryOfRegistration(
                $this->vehicleCatalog->getCountryOfRegistrationByCode(self::DEFAULT_COUNTRY_OF_REGISTRATION)
            )
            ->setFuelType($fuelType)
            ->setWeight($dvlaVehicle->getDesignedGrossWeight())
            ->setDvlaVehicleId($dvlaVehicle->getId())
            ->setFreeTextMakeName($makeModelName);

        $this->importWeight($dvlaVehicle, $vehicle);

        if ($dvlaVehicle->getSecondaryColour()) {
            $vehicle->setSecondaryColour(
                $this->vehicleCatalog->getColourByCode($dvlaVehicle->getSecondaryColour())
            );
        }

        $this->vehicleRepository->save($vehicle);
        $dvlaVehicle->setVehicle($vehicle);
        $this->dvlaVehicleRepository->save($dvlaVehicle);

        $v5cDocumentNumber = $dvlaVehicle->getV5DocumentNumber();
        if (null !== $v5cDocumentNumber) {
            // VM-7220: Create corresponding V5C record.
            $vehicleV5C = (new VehicleV5C())
                ->setVehicle($vehicle)
                ->setV5cRef($dvlaVehicle->getV5DocumentNumber())
                ->setFirstSeen(new \DateTime());
            $this->vehicleV5CRepository->save($vehicleV5C);
        }

        return $vehicle;
    }

    /**
     * @param Person  $person
     * @param Vehicle $vehicle
     * @param int     $vehicleClassCode
     * @param int     $primaryColourCode
     * @param int     $secondaryColourCode
     * @param string  $fuelTypeCode
     */
    public function logDvlaVehicleImportChanges(
        Person $person,
        Vehicle $vehicle,
        $vehicleClassCode,
        $primaryColourCode,
        $secondaryColourCode,
        $fuelTypeCode
    ) {
        $vehicleClass = $this->vehicleCatalog->getVehicleClassByCode($vehicleClassCode);

        $importChanges = (new DvlaVehicleImportChangeLog())
            ->setTester($person)
            ->setVehicle($vehicle)
            ->setVehicleClass($vehicleClass)
            ->setColour($primaryColourCode)
            ->setSecondaryColour($secondaryColourCode)
            ->setFuelType($fuelTypeCode)
            ->setImported(new \DateTime());

        $this->dvlaVehicleImportChangesRepository->save($importChanges);
    }

    /**
     * @param DvlaVehicle $dvlaVehicle
     * @param Vehicle     $vehicle
     */
    private function importWeight(DvlaVehicle $dvlaVehicle, Vehicle $vehicle)
    {
        $grossWeight = $dvlaVehicle->getDesignedGrossWeight();
        if (!empty($grossWeight)) {
            $vehicle->setWeight($grossWeight);
            $vehicle->setWeightSource($this->vehicleCatalog->getWeightSourceByCode(WeightSourceCode::DGW));

            return;
        }

        $unladenWeight = $dvlaVehicle->getUnladenWeight();
        if (!empty($unladenWeight)) {
            $vehicle->setWeight($unladenWeight + self::UNLADEN_WEIGHT_UPLIFT);
            $vehicle->setWeightSource($this->vehicleCatalog->getWeightSourceByCode(WeightSourceCode::UNLADEN));
        }
    }

    /**
     * @param array $data
     *
     * @return Vehicle
     */
    private function mapVehicle($data)
    {
        $vehDic = $this->vehicleCatalog;
        $model = $vehDic->findModel($data['make'], $data['model']);
        $make = $vehDic->getMakeByCode($data['make']);
        $vehicle = (new Vehicle())
            ->setVin($data['vin'])
            ->setRegistration($data['registrationNumber'])
            ->setCylinderCapacity($data['cylinderCapacity'])
            ->setFirstUsedDate(DateUtils::toDate($data['dateOfFirstUse']))
            ->setColour($vehDic->getColourByCode($data['colour']))
            ->setFuelType($vehDic->getFuelTypeByCode($data['fuelType']))
            ->setCountryOfRegistration($vehDic->getCountryOfRegistration($data['countryOfRegistration'], true))
            ->setTransmissionType($vehDic->getTransmissionType($data['transmissionType']))
            ->setVehicleClass($vehDic->getVehicleClassByCode($data['testClass']));

        if (is_null($make)) {
            $vehicle->setFreeTextMakeName($data['makeOther']);
            $vehicle->setFreeTextModelName($data['modelOther']);
        } else {
            $vehicle->setMake($make);
            if ($model) {
                $vehicle->setModel($model);
            } else {
                $vehicle->setFreeTextModelName($data['modelOther']);
            }
        }

        if (!empty($data['manufactureDate'])) {
            $vehicle->setManufactureDate(DateUtils::toDate($data['manufactureDate']));
        }
        if (!empty($data['firstRegistrationDate'])) {
            $vehicle->setFirstRegistrationDate(DateUtils::toDate($data['firstRegistrationDate']));
        }

        if (!empty($data['modelType'])) {
            $vehicle->setModelDetail($vehDic->getModelDetail($data['modelType'], true));
        }
        if (!empty($data['bodyType'])) {
            $vehicle->setBodyType($vehDic->findBodyTypeByCode($data['bodyType']));
        }
        if (!empty($data['secondaryColour'])) {
            $vehicle->setSecondaryColour($vehDic->getColourByCode($data['secondaryColour'], true));
        }

        if (!empty($data['emptyVrmReason'])) {
            $vehicle->setEmptyVrmReason($this->vehicleCatalog->getEmptyVrmReasonByCode($data['emptyVrmReason']));
        }
        if (!empty($data['emptyVinReason'])) {
            $vehicle->setEmptyVinReason($this->vehicleCatalog->getEmptyVinReasonByCode($data['emptyVinReason']));
        }

        return $vehicle;
    }
}
