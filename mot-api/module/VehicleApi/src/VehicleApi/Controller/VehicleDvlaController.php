<?php

namespace VehicleApi\Controller;

use DvsaCommonApi\Controller\AbstractDvsaRestfulController;
use DvsaCommonApi\Model\ApiResponse;
use Doctrine\ORM\EntityManager;
use VehicleApi\Service\VehicleService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * Class VehicleDvlaController
 *
 * @package VehicleApi\Controller
 */
class VehicleDvlaController extends AbstractDvsaRestfulController
{
    public function get($id)
    {
        $data = $this->getVehicleService()->getDvlaVehicleData($id);

        return ApiResponse::jsonOk($data);
    }

    /**
     * @return VehicleService
     */
    private function getVehicleService()
    {
        return $this->getServiceLocator()->get(VehicleService::class);
    }
}
