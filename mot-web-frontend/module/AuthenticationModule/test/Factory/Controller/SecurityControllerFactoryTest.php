<?php
/**
 * This file is part of the DVSA MOT Frontend project.
 *
 * @link http://gitlab.clb.npm/mot/mot
 */

namespace Dvsa\Mot\Frontend\AuthenticationModuleTest\Listener\Factory;

use Dvsa\Mot\Frontend\AuthenticationModule\Controller\SecurityController;
use Dvsa\Mot\Frontend\AuthenticationModule\Factory\Controller\SecurityControllerFactory;
use Dvsa\Mot\Frontend\AuthenticationModule\OpenAM\OpenAMAuthenticator;
use Dvsa\Mot\Frontend\AuthenticationModule\Service\GotoUrlService;
use Dvsa\Mot\Frontend\AuthenticationModule\Service\IdentitySessionStateService;
use Dvsa\Mot\Frontend\AuthenticationModule\Service\WebAuthenticationCookieService;
use DvsaCommonTest\TestUtils\ServiceFactoryTestHelper;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Request;

class SecurityControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryCreatesInstance()
    {
        ServiceFactoryTestHelper::testCreateServiceForCM(
            SecurityControllerFactory::class,
            SecurityController::class,
            [
                'Request' => Request::class,
                OpenAMAuthenticator::class,
                GotoUrlService::class,
                'tokenService' => WebAuthenticationCookieService::class,
                IdentitySessionStateService::class,
                'ZendAuthenticationService' => AuthenticationService::class,
            ]
        );
    }
}