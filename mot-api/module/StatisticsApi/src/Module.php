<?php

namespace Dvsa\Mot\Api\StatisticsApi;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/../config/services.config.php';
    }
}
