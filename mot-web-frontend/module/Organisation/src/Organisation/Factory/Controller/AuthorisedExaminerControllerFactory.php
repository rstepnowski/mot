<?php

namespace Organisation\Factory\Controller;

use DvsaClient\MapperFactory;
use Organisation\Controller\AuthorisedExaminerController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AuthorisedExaminerControllerFactory.
 */
class AuthorisedExaminerControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $controllerManager
     *
     * @return AuthorisedExaminerController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /* @var ServiceLocatorInterface $serviceLocator */
        $serviceLocator    = $controllerManager->getServiceLocator();

        return new AuthorisedExaminerController(
            $serviceLocator->get('AuthorisationService'),
            $serviceLocator->get(MapperFactory::class),
            $serviceLocator->get('MotIdentityProvider')
        );
    }
}