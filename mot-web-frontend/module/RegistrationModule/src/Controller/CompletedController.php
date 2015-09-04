<?php
/**
 * This file is part of the DVSA MOT Frontend project.
 *
 * @link http://gitlab.clb.npm/mot/mot
 */

namespace Dvsa\Mot\Frontend\RegistrationModule\Controller;

use Dvsa\Mot\Frontend\RegistrationModule\Service\RegisterUserService;
use Dvsa\Mot\Frontend\RegistrationModule\Service\RegistrationSessionService;
use Dvsa\Mot\Frontend\RegistrationModule\Service\RegistrationStepService;
use Dvsa\Mot\Frontend\RegistrationModule\Step\DetailsStep;
use DvsaCommon\InputFilter\Registration\DetailsInputFilter;
use DvsaCommon\UrlBuilder\AccountUrlBuilderWeb;
use Zend\View\Model\ViewModel;

/**
 * Completed Controller.
 */
class CompletedController extends AbstractRegistrationController
{
    const PAGE_TITLE = 'Your account has been created';
    const PAGE_TITLE_FAILURE = 'Your account has not been created';

    /**
     * @var RegisterUserService
     */
    protected $registerUserService;

    /**
     * @var RegistrationSessionService
     */
    protected $session;

    private $helpdeskConfig;

    /**
     * @param RegistrationStepService    $registrationService
     * @param RegisterUserService        $registerUserService
     * @param RegistrationSessionService $session
     */
    public function __construct(
        RegistrationStepService $registrationService,
        RegisterUserService $registerUserService,
        RegistrationSessionService $session,
        array $helpdeskConfig
    ) {
        parent::__construct($registrationService);
        $this->registerUserService = $registerUserService;
        $this->session = $session;
        $this->helpdeskConfig = $helpdeskConfig;
    }

    /**
     * @return \Zend\Http\Response
     */
    public function indexAction()
    {
        if ($this->registerUserService->registerUser($this->session->toArray())) {
            return $this->redirect()->toRoute('account-register/complete-registration-success');
        } else {
            return $this->redirect()->toRoute('account-register/complete-registration-failure');
        }
    }

    /**
     * This is the end of the journey.. kill the session and were done.
     *
     * The email address will only get displayed to the user once, if they refresh the page it will go
     *
     * @return ViewModel
     */
    public function successAction()
    {
        if (false === $this->checkValidSession()) {
            $this->redirectToStart();
        }

        $this->setLayout(self::PAGE_TITLE);

        $values = $this->session->toArray();

        $emailAddress = (isset($values[DetailsStep::STEP_ID][DetailsInputFilter::FIELD_EMAIL]))
            ? $values[DetailsStep::STEP_ID][DetailsInputFilter::FIELD_EMAIL]
            : null;

        $this->session->destroy();

        return new ViewModel([
            'signInUrl' => AccountUrlBuilderWeb::signIn(),
            'email'     => $emailAddress,
            'helpdesk'  => $this->helpdeskConfig,
        ]);
    }

    /**
     * Do NOT kill the session on fail. The user has the ability to go
     * back and modify settings.
     *
     * @return ViewModel
     */
    public function failAction()
    {
        if (false === $this->checkValidSession()) {
            $this->redirectToStart();
        }

        $this->setLayout(self::PAGE_TITLE_FAILURE, self::DEFAULT_SUB_TITLE);

        return new ViewModel([
            'helpdesk'  => $this->helpdeskConfig,
        ]);
    }

    /**
     * If there is no valid session, we should go to the journey start.
     *
     * @return \Zend\Http\Response
     */
    protected function checkValidSession()
    {
        $values = $this->session->toArray();

        return !(is_array($values) && count($values) === 0);
    }

    protected function redirectToStart()
    {
        return $this->redirect()->toRoute('account-register/create-an-account');
    }
}
