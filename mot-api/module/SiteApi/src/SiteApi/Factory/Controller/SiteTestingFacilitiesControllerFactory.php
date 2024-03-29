<?php

namespace SiteApi\Factory\Controller;

use Doctrine\ORM\EntityManager;
use SiteApi\Controller\SiteTestingFacilitiesController;
use SiteApi\Service\SiteTestingFacilitiesService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Class SiteControllerFactory
 * @package SiteApi\Factory\Controller
 */
class SiteTestingFacilitiesControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $controllerManager
     * @return SiteTestingFacilitiesController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /** @var ServiceManager $serviceLocator */
        $serviceLocator = $controllerManager->getServiceLocator();
        /** @var SiteTestingFacilitiesService $siteTestingFacilitiesService */
        $siteTestingFacilitiesService = $serviceLocator->get(SiteTestingFacilitiesService::class);

        return new SiteTestingFacilitiesController(
            $siteTestingFacilitiesService
        );
    }
}
