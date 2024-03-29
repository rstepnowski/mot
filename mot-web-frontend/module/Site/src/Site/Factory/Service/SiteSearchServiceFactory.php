<?php

namespace Site\Factory\Service;

use Site\Service\SiteSearchService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SiteSearchServiceFactory
 * @package Site\Factory\Service
 */
class SiteSearchServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $controllerManager
     * @return SiteSearchService
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        return new SiteSearchService();
    }
}