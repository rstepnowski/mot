<?php

namespace AccountApi\Controller;

use AccountApi\Service\ClaimService;
use AccountApi\Service\Exception\OpenAmChangePasswordException;
use DvsaCommonApi\Controller\AbstractDvsaRestfulController;
use DvsaCommonApi\Model\ApiResponse;
use DvsaCommonApi\Service\Exception\ServiceException;
use SebastianBergmann\Exporter\Exception;
use Zend\View\Model\JsonModel;

/**
 * Claim account controller.
 */
class ClaimController extends AbstractDvsaRestfulController
{
    /**
     * @param mixed $id
     *
     * @return JsonModel
     */
    public function get($id)
    {
        /** @var \AccountApi\Service\ClaimService $claimService */
        $claimService = $this->getServiceLocator()->get(ClaimService::class);
        $response = $claimService->generateClaimAccountData();

        return ApiResponse::jsonOk($response);
    }

    /**
     * @param int        $id
     * @param array|null $data
     *
     * @throws \DvsaCommon\Exception\UnauthorisedException
     * @throws \Exception
     *
     * @return \Zend\View\Model\JsonModel
     */
    public function update($id, $data)
    {
        /** @var \AccountApi\Service\ClaimService $claimService */
        $claimService = $this->getServiceLocator()->get(ClaimService::class);
        try {
            $response = $claimService->save($data);

            return ApiResponse::jsonOk($response);
        } catch (OpenAmChangePasswordException $e) {
            $config      = $this->getServiceLocator()->get('Config');
            $name        = isset($config['helpdesk']['name'])
                ? $config['helpdesk']['name']
                : 'DVSA Helpdesk';
            $phoneNumber = isset($config['helpdesk']['phoneNumber'])
                ? $config['helpdesk']['phoneNumber']
                : '01792 454397';

            $statusCode       = $e->getCode();
            $displayMessage   = sprintf("This password has already been used for your account." . PHP_EOL .
                "Please choose a new password."  . PHP_EOL .
                PHP_EOL .
                "If you continue to have problems, please contact the %s on %s who will be able to advise you.",
                $name, $phoneNumber);

            /*
             * NOTE: Add 'message' => $e->getMessage() to the $errors array if we need the OpenAM message in the
             * Web Frontend.
             */
            $errors           = [
                'step'              => 'confirmPassword',
                'displayMessage'    => $displayMessage,
            ];

            $serviceException = new ServiceException($displayMessage, $statusCode, $e);
            /*
             * Serialize? A null statusCode? Yep. You are reading it right. Take a look at the Frontend API client and
             * you will found out why. And while you are looking at it, can you make it better (please?), because this
             * poor dev didn't have the time. Thanks!
             */
            $serviceException->addError(serialize($errors), null, $displayMessage);

            throw $serviceException;
        }
    }
}
