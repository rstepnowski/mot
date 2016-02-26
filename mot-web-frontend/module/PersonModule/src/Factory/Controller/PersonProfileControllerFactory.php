<?php
/**
 * This file is part of the DVSA MOT Frontend project.
 *
 * @link http://gitlab.clb.npm/mot/mot
 */

namespace Dvsa\Mot\Frontend\PersonModule\Factory\Controller;

use Application\Data\ApiPersonalDetails;
use Application\Service\CatalogService;
use Dashboard\Authorisation\ViewTradeRolesAssertion;
use Dashboard\Data\ApiDashboardResource;
use Dvsa\Mot\Frontend\PersonModule\Controller\PersonProfileController;
use Dvsa\Mot\Frontend\PersonModule\Security\PersonProfileGuardBuilder;
use Dvsa\Mot\Frontend\PersonModule\View\ContextProvider;
use DvsaClient\MapperFactory;
use UserAdmin\Service\UserAdminSessionManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Factory for PersonProfileController instances.
 */
class PersonProfileControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $controllerManager
     *
     * @return PersonProfileController()
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /** @var ServiceManager $serviceLocator */
        $serviceLocator = $controllerManager->getServiceLocator();

        /** @var ApiPersonalDetails $apiPersonalDetails */
        $apiPersonalDetails = $serviceLocator->get(ApiPersonalDetails::class);

        /** @var ApiDashboardResource $apiDashboardResource */
        $apiDashboardResource = $serviceLocator->get(ApiDashboardResource::class);

        /** @var CatalogService $catalogService */
        $catalogService = $serviceLocator->get('CatalogService');

        /** @var UserAdminSessionManager $userAdminSessionManager */
        $userAdminSessionManager = $serviceLocator->get(UserAdminSessionManager::class);

        /** @var ViewTradeRolesAssertion $viewTradeRolesAssertion */
        $viewTradeRolesAssertion = $serviceLocator->get(ViewTradeRolesAssertion::class);

        /** @var PersonProfileGuardBuilder $personProfileGuardBuilder */
        $personProfileGuardBuilder = $serviceLocator->get(PersonProfileGuardBuilder::class);

        /** @var MapperFactory $mapperFactory */
        $mapperFactory = $serviceLocator->get(MapperFactory::class);

        /** @var ContextProvider $contextProvider */
        $contextProvider = $serviceLocator->get(ContextProvider::class);

        return new PersonProfileController($apiPersonalDetails, $apiDashboardResource, $catalogService,
            $userAdminSessionManager, $viewTradeRolesAssertion, $personProfileGuardBuilder, $mapperFactory,
            $contextProvider);
    }
}