<?php

namespace Core\Service;

use Dvsa\Mot\Frontend\AuthenticationModule\Model\Identity;
use DvsaCommon\Auth\MotIdentityProviderInterface;
use Zend\Authentication\AuthenticationService;

/**
 * MotIdentityProviderInterface that delegates to Zend Framework
 */
interface MotFrontendIdentityProviderInterface extends MotIdentityProviderInterface
{
    /** @return Identity */
    public function getIdentity();
}
