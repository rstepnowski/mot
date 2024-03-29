<?php

namespace SiteTest\Factory\Controller;

use DvsaCommon\Validator\UsernameValidator;
use DvsaCommonTest\Bootstrap;
use Site\Controller\RoleController;
use Site\Factory\Controller\RoleControllerFactory;

/**
 * Class RoleControllerFactoryTest.
 */
class RoleControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryReturnsRoleControllerInstance()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $usernameValidatorMock = $this
            ->getMockBuilder(UsernameValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->setService(UsernameValidator::class, $usernameValidatorMock);

        $serviceManager->setService('HTMLPurifier', $this->getMock('HTMLPurifier'));

        $controllerManager = $this->getMock('Zend\Mvc\Controller\ControllerManager');
        $controllerManager->expects($this->any())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceManager));
        $factory = new RoleControllerFactory();

        $this->assertInstanceOf(RoleController::class, $factory->createService($controllerManager));
    }
}
