<?php

namespace DvsaAuthentication\Authentication\Adapter\OpenAM;

use Dvsa\OpenAM\Exception\OpenAMClientException;
use Dvsa\OpenAM\Exception\OpenAMUnauthorisedException;
use Dvsa\OpenAM\OpenAMClientInterface;
use DvsaApplicationLogger\TokenService\TokenServiceInterface;
use DvsaAuthentication\Identity\OpenAM\OpenAMIdentityByTokenResolver;
use DvsaAuthentication\IdentityFactory;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Http\Exception\InvalidArgumentException;
use Zend\Http\PhpEnvironment\Request;
use Zend\Log\LoggerInterface;

class OpenAMApiTokenBasedAdapter implements AdapterInterface
{
    private $identityByTokenResolver;
    private $tokenService;
    private $logger;


    public function __construct(
        OpenAMIdentityByTokenResolver $resolver,
        LoggerInterface $logger,
        $tokenService
    ) {
        $this->identityByTokenResolver = $resolver;
        $this->logger = $logger;
        $this->tokenService = $tokenService;
    }

    public function authenticate()
    {
        $token = $this->tokenService->parseToken();
        if (!$token) {
            $this->logger->info('Token not found or invalid!');
            return self::invalidTokenResult();
        }

        $resolvedIdentity = $this->identityByTokenResolver->resolve($token);
        if (is_null($resolvedIdentity)) {
            return self::identityResolutionFailedResult();
        }
        return new Result(Result::SUCCESS, $resolvedIdentity);
    }

    private static function invalidTokenResult()
    {
        return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['Valid token required']);
    }

    private static function identityResolutionFailedResult()
    {
        return new Result(Result::FAILURE, null, ['Identity resolution failed']);
    }
}
