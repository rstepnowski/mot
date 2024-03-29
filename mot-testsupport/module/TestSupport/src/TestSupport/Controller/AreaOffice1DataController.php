<?php

namespace TestSupport\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use DvsaCommon\Constants\Role;
use TestSupport\Helper\TestSupportAccessTokenManager;
use TestSupport\Service\AccountDataService;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use TestSupport\Service\AreaOffice1Service;

/**
 * Creates User account with AREA-OFFICE-1 role for use by tests.
 * Should not be deployed in production.
 */
class AreaOffice1DataController extends BaseTestSupportRestfulController
{
    /**
     * @param mixed $data including "diff" string to differentiate scheme management users
     * @return JsonModel username of new AO1 user
     */
    public function create($data)
    {
        $areaOffice1Service = $this->getServiceLocator()->get(AreaOffice1Service::class);
        $resultJson = $areaOffice1Service->create($data);
        return $resultJson;
    }
}
