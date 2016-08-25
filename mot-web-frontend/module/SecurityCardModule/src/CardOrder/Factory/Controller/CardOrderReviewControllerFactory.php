<?php

namespace Dvsa\Mot\Frontend\SecurityCardModule\CardOrder\Factory\Controller;

use Application\Data\ApiPersonalDetails;
use Dvsa\Mot\Frontend\SecurityCardModule\CardOrder\Action\CardOrderReviewAction;
use Dvsa\Mot\Frontend\SecurityCardModule\Security\SecurityCardGuard;
use Dvsa\Mot\Frontend\SecurityCardModule\Service\SecurityCardService;
use Dvsa\Mot\Frontend\SecurityCardModule\CardOrder\Controller\CardOrderReviewController;
use Dvsa\Mot\Frontend\SecurityCardModule\CardOrder\Service\OrderNewSecurityCardSessionService;
use Dvsa\Mot\Frontend\SecurityCardModule\Support\TwoFaFeatureToggle;
use DvsaClient\Mapper\TesterGroupAuthorisationMapper;
use DvsaCommon\Auth\MotIdentityProvider;
use Zend\Http\Request;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CardOrderReviewControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CardOrderReviewController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();

        /** @var OrderNewSecurityCardSessionService $securityCardSessionService */
        $securityCardSessionService = $serviceLocator->get(OrderNewSecurityCardSessionService::class);

        $action = $serviceLocator->get(CardOrderReviewAction::class);

        /** @var MotIdentityProvider $identityProvider */
        $identityProvider = $serviceLocator->get('MotIdentityProvider');

        return new CardOrderReviewController(
            $securityCardSessionService,
            $action,
            $identityProvider->getIdentity()
        );
    }
}
