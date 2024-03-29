<?php

namespace UserAdminTest\Factory\Controller;

use Core\Service\MotFrontendAuthorisationServiceInterface;
use Dvsa\Mot\Frontend\PersonModule\View\ContextProvider;
use DvsaClient\Mapper\TesterGroupAuthorisationMapper;
use UserAdmin\Controller\DrivingLicenceController;
use UserAdmin\Factory\Controller\DrivingLicenceControllerFactory;
use UserAdmin\Service\HelpdeskAccountAdminService;
use UserAdmin\Service\PersonRoleManagementService;
use UserAdmin\Service\UserAdminSessionService;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;

/**
 * Test for {@link \UserAdmin\Factory\Controller\DrivingLicenceControllerFactory}.
 */
class DrivingLicenceControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $serviceManager = new ServiceManager();

        $accountAdminServiceMock = $this->getMock(HelpdeskAccountAdminService::class, [], [], '', false);
        $authorisationServiceMock = $this->getMock(MotFrontendAuthorisationServiceInterface::class, [], [], '', false);
        $testerGroupAuthorisationMapperMock = $this->getMock(TesterGroupAuthorisationMapper::class, [], [], '', false);
        $userAdminSessionServiceMock = $this->getMock(UserAdminSessionService::class, [], [], '', false);
        $personRoleManagementServiceMock = $this->getMock(PersonRoleManagementService::class, [], [], '', false);
        $contextProviderMock = $this->getMock(ContextProvider::class, [], [], '', false);

        $serviceManager->setService(HelpdeskAccountAdminService::class, $accountAdminServiceMock);
        $serviceManager->setService("AuthorisationService", $authorisationServiceMock);
        $serviceManager->setService(TesterGroupAuthorisationMapper::class, $testerGroupAuthorisationMapperMock);
        $serviceManager->setService(UserAdminSessionService::class, $userAdminSessionServiceMock);
        $serviceManager->setService(PersonRoleManagementService::class, $personRoleManagementServiceMock);
        $serviceManager->setService(ContextProvider::class, $contextProviderMock);

        $plugins = $this->getMock(ControllerManager::class);
        $plugins->expects($this->any())
                ->method('getServiceLocator')
                ->will($this->returnValue($serviceManager));

        $factory = new DrivingLicenceControllerFactory;
        $result = $factory->createService($plugins);

        $this->assertInstanceOf(DrivingLicenceController::class, $result);
    }
}
