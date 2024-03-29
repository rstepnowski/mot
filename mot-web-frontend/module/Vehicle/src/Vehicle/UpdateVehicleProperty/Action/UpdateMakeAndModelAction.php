<?php

namespace Vehicle\UpdateVehicleProperty\Action;

use Core\Action\ViewActionResult;
use Core\Controller\AbstractAuthActionController;
use Core\FormWizard\StepResult;
use Dvsa\Mot\ApiClient\Service\VehicleService;
use DvsaCommon\Auth\MotAuthorisationServiceInterface;
use DvsaCommon\Auth\PermissionInSystem;
use DvsaCommon\Factory\AutoWire\AutoWireableInterface;
use DvsaCommon\Obfuscate\ParamObfuscator;
use Vehicle\UpdateVehicleProperty\Context\UpdateVehicleContext;
use Vehicle\UpdateVehicleProperty\Form\Wizard\Context;
use Vehicle\UpdateVehicleProperty\Form\Wizard\UpdateMakeAndModelWizard;

class UpdateMakeAndModelAction implements AutoWireableInterface
{
    private $vehicleService;
    private $paramObfuscator;
    private $wizard;
    private $authorisationService;

    public function __construct(
        VehicleService $vehicleService,
        ParamObfuscator $paramObfuscator,
        UpdateMakeAndModelWizard $wizard,
        MotAuthorisationServiceInterface $authorisationService
    )
    {
        $this->vehicleService = $vehicleService;
        $this->paramObfuscator = $paramObfuscator;
        $this->wizard = $wizard;
        $this->authorisationService = $authorisationService;
    }

    public function execute($stepName, $obfuscatedVehicleId, $isPost, $formUuid = null, array $formData = [], $requestUrl)
    {
        $this->assertGranted();

        $vehicleId = (int)$this->paramObfuscator->deobfuscateEntry(ParamObfuscator::ENTRY_VEHICLE_ID, $obfuscatedVehicleId);
        $vehicle = $this->getVehicle($vehicleId);
        $context = new UpdateVehicleContext($vehicle, $obfuscatedVehicleId, $requestUrl);

        $this->wizard->setContext($context);
        $this->wizard->setSessionStoreKey("update-make-and-model" . $obfuscatedVehicleId);
        $stepResult = $this->wizard->process($stepName, $isPost, $formUuid, $formData);

        if ($stepResult instanceof StepResult) {
            return $this->buildActionResult($stepResult);
        }

        return $stepResult;
    }

    protected function assertGranted()
    {
        $this->authorisationService->assertGranted(PermissionInSystem::VEHICLE_UPDATE);
    }

    private function buildActionResult(StepResult $result)
    {
        $actionResult = new ViewActionResult();
        $actionResult->setViewModel($result->getViewModel());
        $actionResult->addErrorMessages($result->getErrorMessages());

        $actionResult->layout()->setTemplate("layout/layout-govuk.phtml");
        $actionResult->setTemplate($result->getTemplate());

        $actionResult->layout()->setPageTitle($result->getStepLayoutData()->getPageTitle());
        $actionResult->layout()->setPageSubTitle($result->getStepLayoutData()->getPageSubTitle());
        $actionResult->layout()->setPageLede($result->getStepLayoutData()->getPageLede());
        $actionResult->layout()->setBreadcrumbs($result->getStepLayoutData()->getBreadcrumbs());

        return $actionResult;
    }

    /**
     * @param $vehicleId
     *
     * @return \Dvsa\Mot\ApiClient\Resource\Item\DvlaVehicle|\Dvsa\Mot\ApiClient\Resource\Item\DvsaVehicle
     * @throws \Exception
     */
    private function getVehicle($vehicleId)
    {
        try {
            $vehicle = $this->vehicleService->getDvsaVehicleById($vehicleId);
        } catch (\Exception $exception) {
            try {
                $vehicle = $this->vehicleService->getDvlaVehicleById($vehicleId);
            } catch (\Exception $exception) {
                throw new \Exception(
                    'No vehicle with id ' . $vehicleId . ' found'
                );
            }
        }

        return $vehicle;
    }
}
