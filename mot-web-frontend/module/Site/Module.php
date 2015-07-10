<?php

namespace Site;

use Site\Service\SiteSearchService;
use Site\Factory\Service\SiteSearchServiceFactory;

/**
 * Class Module
 *
 * @package Site
 */
class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                SiteSearchService::class => SiteSearchServiceFactory::class,
            ]
        ];
    }

    public function getAutoloaderConfig()
    {
    }
}
