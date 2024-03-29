<?php

namespace Dvsa\Mot\Frontend\PersonModule\Factory\Controller;


use Application\Data\ApiPersonalDetails;
use Core\Service\SessionService;
use Dvsa\Mot\Frontend\PersonModule\Controller\ChangeAddressController;
use Dvsa\Mot\Frontend\PersonModule\Security\PersonProfileGuardBuilder;
use Dvsa\Mot\Frontend\PersonModule\View\ContextProvider;
use Dvsa\Mot\Frontend\PersonModule\View\PersonProfileUrlGenerator;
use DvsaClient\MapperFactory;
use UserAdmin\Service\HelpdeskAccountAdminService;
use UserAdmin\Service\UserAdminSessionService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

class ChangeAddressControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /**
         * @var ServiceManager
         */
        $serviceLocator = $controllerManager->getServiceLocator();

        /**
         * @var PersonProfileGuardBuilder $personProfileGuard
         */
        $personProfileGuardBuilder = $serviceLocator->get(PersonProfileGuardBuilder::class);

        /**
         * @var HelpDeskAccountAdminService $helpDeskAccountAdminService
         */
        $helpDeskAccountAdminService = $serviceLocator->get(HelpDeskAccountAdminService::class);

        /**
         * @var PersonProfileUrlGenerator $personProfileUrlBuilder
         */
        $personProfileUrlBuilder = $serviceLocator->get(PersonProfileUrlGenerator::class);

        /**
         * @var ContextProvider $contextProvider
         */
        $contextProvider = $serviceLocator->get(ContextProvider::class);

        /**
         * @var ApiPersonalDetails $personalDetailsService
         */
        $personalDetailsService = $serviceLocator->get(ApiPersonalDetails::class);

        /**
         * @var MapperFactory $mapperFactory
         */

        $mapperFactory = $serviceLocator->get(MapperFactory::class);

        /**
         * @var SessionService $sessionService
         */
        $sessionService = $serviceLocator->get(UserAdminSessionService::class);

        return new ChangeAddressController(
            $personProfileGuardBuilder,
            $helpDeskAccountAdminService,
            $personProfileUrlBuilder,
            $contextProvider,
            $personalDetailsService,
            $mapperFactory,
            $sessionService
        );
    }
}