<?php

namespace DvsaMotEnforcement\Controller;

use Application\Model\DvsaConfigCatalog;
use Application\Service\CatalogService;
use Dashboard\Controller\UserHomeController;
use DvsaCommon\Auth\PermissionInSystem;
use DvsaCommon\Domain\MotTestType;
use DvsaCommon\Dto\Common\MotTestDto;
use DvsaCommon\Dto\Person\PersonDto;
use DvsaCommon\Enum\MotTestTypeCode;
use DvsaCommon\HttpRestJson\Exception\RestApplicationException;
use DvsaCommon\Obfuscate\ParamObfuscator;
use DvsaCommon\UrlBuilder\MotTestUrlBuilder;
use DvsaCommon\UrlBuilder\UrlBuilder;
use DvsaCommon\Utility\ArrayUtils;
use DvsaMotEnforcement\Model\MotTest as MotTestModel;
use DvsaMotTest\Controller\AbstractDvsaMotTestController;
use DvsaMotTest\Model\OdometerReadingViewObject;
use Zend\View\Model\ViewModel;

/**
 * Class MotTestController
 *
 * @package DvsaMotEnforcement\Controller
 */
class MotTestController extends AbstractDvsaMotTestController
{
    const MOT_TEST_ABORTED_MESSAGE = "The mot test was successfully aborted";
    const NO_RESULTS_FOUND_MESSAGE = 'No results found';
    const REINSPECTION_SAVE_MSG = 'The reinspection assessment outcome and details of the test differences have been saved';

    /** @var  ParamObfuscator */
    private $paramObfuscator;

    public function __construct(
        ParamObfuscator $paramObfuscator
    ) {
        $this->paramObfuscator = $paramObfuscator;
    }

    /**
     * Show an enforcement view of an MOT Test Result.
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function displayTestSummaryAction()
    {
        $this->layout('layout/layout_enforcement');

        $this->assertGranted(PermissionInSystem::DVSA_SITE_SEARCH);

        $motTestNumber = (int)$this->params()->fromRoute('motTestNumber', 0);
        $motDetails = null;
        $isDemoMotTest = false;
        $isNonMotTest = false;
        $tester = null;

        /** @var MotTestDto $motDetails */
        $motDetails = $this->tryGetMotTestOrAddErrorMessages($motTestNumber);

        if (!empty($motDetails)) {
            /** @var PersonDto $testerDto */
            $testerDto = $motDetails->getTester();
            $testerUsername = $testerDto->getUsername();
            // Fetch the User that did the test
            $userSearchUrl = sprintf('user/%s', $testerUsername);
            $userResult = $this->getRestClient()->get($userSearchUrl);
            $tester = $userResult['data'];
            $isDemoMotTest = MotTestType::isDemo($motDetails->getTestType()->getCode());
            $isNonMotTest = MotTestType::isNonMotTypes($motDetails->getTestType()->getCode());
        }

        /** @var CatalogService $catalogService */
        $catalogService = $this->getCatalogService();

        $motTestTypes = ArrayUtils::filter(
            $catalogService->getData()['motTestType'],
            function ($motTestTypeAsArray) {
                return MotTestType::isReinspection($motTestTypeAsArray['code']);
            }
        );

        $brakeTestTypeCode2Name = [];
        foreach ($catalogService->getData()['brakeTestType'] as $breakTestType){
            $brakeTestTypeCode2Name[$breakTestType['code']] = $breakTestType['name'];
        }

        $odometerReading = OdometerReadingViewObject::create();
        if ($motDetails->getOdometerReading() !== null) {
            $odometerReading->setOdometerReadingValuesMap($motDetails->getOdometerReading());
        }

        //  --  prepare back url    --
        $searchType = $this->params()->fromQuery('type', 'vts');
        $vehicleId = $this->params()->fromQuery('vehicleId', '');
        $summaryParams = [
            'type'       => $searchType,
            'search'     => $this->params()->fromQuery('search'),
            'month1'     => $this->params()->fromQuery('month1'),
            'year1'      => $this->params()->fromQuery('year1'),
            'month2'     => $this->params()->fromQuery('month2'),
            'year2'      => $this->params()->fromQuery('year2'),
            'backTo'     => $this->params()->fromQuery('backTo'),
            'oneResult'  => $this->params()->fromQuery('oneResult', false),
        ];

        return new ViewModel(
            [
                'tester'                 => $tester,
                'motDetails'             => $motDetails,
                'motTestNumber'          => $motTestNumber,
                'motTestTypes'           => $motTestTypes,
                'odometerReading'        => $odometerReading,
                'searchType'             => $searchType,
                'vehicleId'              => $vehicleId,
                'summaryParams'          => $summaryParams,
                'isDemoMotTest'          => $isDemoMotTest,
                'brakeTestTypeCode2Name' => $brakeTestTypeCode2Name,
                'isNonMotTest'           => $isNonMotTest,
            ]
        );
    }

    /**
     * Start an MOT reinspection by a Vehicle Examiner
     *
     * @return \Zend\Http\Response
     */
    public function startInspectionAction()
    {
        $this->assertGranted(PermissionInSystem::MOT_TEST_REINSPECTION_PERFORM);

        /* MOT Test Id */
        $motTestNumber = (int)$this->params()->fromRoute('motTestNumber', 0);
        $motTestType = $this->params()->fromPost('motTestType');
        $complaintRef = $this->params()->fromPost('complaintRef');
        $motTest = null;

        /** @var MotTestDto $motTest */
        $motTest = $this->tryGetMotTestOrAddErrorMessages($motTestNumber);
        if ($motTest) {
            $postData = $this->preparePostData($motTest, $motTestType, $complaintRef);

            try {
                $apiUrl = MotTestUrlBuilder::motTest()->toString();
                $result = $this->getRestClient()->post($apiUrl, $postData);

                $createMotTestResult = $result['data'];
                $newMotTestId = $createMotTestResult['motTestNumber'];

                return $this->redirect()->toRoute(
                    'mot-test',
                    [
                        'controller'    => 'MotTest',
                        'action'        => 'index',
                        'motTestNumber' => $newMotTestId,
                    ]
                );
            } catch (RestApplicationException $e) {
                $this->addErrorMessages($e->getDisplayMessages());
            }
        }

        return $this->redirect()->toRoute(
            'mot-test',
            [
                'controller'    => 'MotTest',
                'action'        => 'index',
                'motTestNumber' => $motTestNumber,
            ]
        );
    }

    /**
     * Allow the VE to see the differences between tests and save their decisions regarding each difference.
     *
     * @return ViewModel
     */
    public function differencesFoundBetweenTestsAction()
    {
        // FIXME: refactor co use init method for all actions inside this controller or even module
        $this->layout('layout/layout_enforcement');

        $this->assertGranted(PermissionInSystem::MOT_TEST_REINSPECTION_PERFORM);

        $motTestNumber = (int)$this->params()->fromRoute('motTestNumber', 0);
        $formErrorData = [];
        $defaultValues = [];

        if ($this->getRequest()->isPost()) {
            try {
                $result = $this->postCompareResult($motTestNumber);

                $resultData = ArrayUtils::tryGet($result, 'data', []);
                $enfResult = ArrayUtils::tryGet($resultData, 'enforcementResult', []);

                if (!empty($enfResult)) {
                    $this->flashMessenger()->addSuccessMessage(self::REINSPECTION_SAVE_MSG);
                }

                return $this->redirect()->toRoute(
                    'enforcement-record-assessment-confirmation',
                    ['motTestNumber' => $motTestNumber, 'resultId' => $enfResult['id']]
                );
            } catch (RestApplicationException $e) {
                $this->addErrorMessages($e->getDisplayMessages());
                $formErrorData = $e->getExpandedErrorData();
                $this->addFormErrorMessagesToSession($e->getFormErrorDisplayMessages());
                $defaultValues = $this->params()->fromPost();
            }
        }

        try {
            $apiUrl = UrlBuilder::create()->motTest()->routeParam('motTestNumber', $motTestNumber)->motTestCompareById()
                ->toString();
            $result = $this->getRestClient()->get($apiUrl);

            $compareResult = $result['data'];
        } catch (RestApplicationException $e) {
            $this->addErrorMessages($e->getDisplayMessages());
            $compareResult = [];
        }

        $viewModel = new ViewModel(
            [
                'defaultValues' => $defaultValues,
                'tester'        => null,
                'compareResult' => $compareResult,
                'formErrors'    => $formErrorData,
                'configCatalog' => new DvsaConfigCatalog($this->getRestClient()),
                'saveButton'    => true,
                'dataCatalog'   => $this->getCatalogService()
            ]
        );

        $viewModel->setTemplate('dvsa-mot-enforcement/mot-test/differences-found-between-tests/index');

        return $viewModel;
    }

    public function abortMotTestAction()
    {
        $this->assertGranted(PermissionInSystem::VE_MOT_TEST_ABORT);

        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        $motTestNumber = (int)$this->params()->fromRoute('motTestNumber', 0);

        if ($request->isPost()) {
            try {
                $data = $request->getPost()->toArray();

                $apiUrl = MotTestUrlBuilder::motTestStatus($motTestNumber)->toString();
                $this->getRestClient()->postJson($apiUrl, $data);

                $this->addInfoMessages(self::MOT_TEST_ABORTED_MESSAGE);

                return $this->redirect()->toRoute(UserHomeController::ROUTE);
            } catch (RestApplicationException $e) {
                $this->addErrorMessages($e->getDisplayMessages());
            }
        }

        return new ViewModel(['motTestNumber' => $motTestNumber]);
    }

    /**
     * This will start the MOT tests comparison
     *
     * @return ViewModel
     */
    public function motTestStartCompareAction()
    {
        $this->layout('layout/layout_enforcement');

        $this->assertGranted(PermissionInSystem::MOT_TEST_REINSPECTION_PERFORM);

        $formErrorData = [];
        $defaultValues = [];

        $motTestNumber = $this->params()->fromRoute('motTestNumber');

        if ($this->getRequest()->isPost()) {
            try {
                $result = $this->postCompareResult($motTestNumber);

                $resultData = ArrayUtils::tryGet($result, 'data', []);
                $enfResult = ArrayUtils::tryGet($resultData, 'enforcementResult', []);

                if (!empty($enfResult)) {
                    $this->flashMessenger()->addSuccessMessage(self::REINSPECTION_SAVE_MSG);
                }

                return $this->redirect()->toRoute(
                    'enforcement-record-assessment-confirmation',
                    ['motTestNumber' => $motTestNumber, 'resultId' => $enfResult['id']]
                );
            } catch (RestApplicationException $e) {
                $this->addErrorMessages($e->getDisplayMessages());
                $formErrorData = $e->getExpandedErrorData();
                $this->addFormErrorMessagesToSession($e->getFormErrorDisplayMessages());
                $defaultValues = $this->params()->fromPost();
            }
        }

        try {
            $apiUrl = UrlBuilder::create()->motTest()->routeParam('motTestNumber', $motTestNumber)->motTestCompareById()
                ->toString();
            $result = $this->getRestClient()->get($apiUrl);

            $compareResult = $result['data'];
        } catch (RestApplicationException $e) {
            $this->addErrorMessages($e->getDisplayMessages());
            $compareResult = [];
        }

        $viewModel = new ViewModel(
            [
                'defaultValues' => $defaultValues,
                'tester'        => null,
                'compareResult' => $compareResult,
                'formErrors'    => $formErrorData,
                'configCatalog' => new DvsaConfigCatalog($this->getRestClient()),
                'saveButton'    => true,
                'dataCatalog'   => $this->getCatalogService()
            ]
        );

        $viewModel->setTemplate('dvsa-mot-enforcement/mot-test/differences-found-between-tests/index.phtml');

        return $viewModel;
    }

    protected function postCompareResult($reinspectionMotTestNumber)
    {
        $data = $this->params()->fromPost();

        $data['reinspectionMotTestNumber'] = $reinspectionMotTestNumber;

        // There may not be any RFRs if the original test is a pass.
        if (!isset($data['mappedRfrs'])) {
            $data['mappedRfrs'] = [];
        }

        foreach ($data['mappedRfrs'] as $rfrId => $rfr) {
            if (empty($rfr['decision'])) {
                $data['mappedRfrs'][$rfrId]['decision'] = null;
            }
            if (empty($rfr['category'])) {
                $data['mappedRfrs'][$rfrId]['category'] = null;
            }
        }

        $result = $this->getRestClient()->postJson(UrlBuilder::enforcementMotTestResult(), $data);
        $apiUrl = UrlBuilder::enforcementMotTestResult($result['data']['id'])->toString();

        return $this->getRestClient()->get($apiUrl);
    }

    /**
     * @param string $motTestType
     *
     * @return string
     * @throws \Exception
     */
    public function getRouteForMotTestType($motTestType)
    {
        switch ($motTestType) {
            case MotTestTypeCode::MOT_COMPLIANCE_SURVEY:
            case MotTestTypeCode::NON_MOT_TEST:
            case MotTestTypeCode::INVERTED_APPEAL:
            case MotTestTypeCode::STATUTORY_APPEAL:
            case MotTestTypeCode::TARGETED_REINSPECTION:
                return 'enforcement-step';
            default:
                throw new \Exception('Unknown inspection report type: ' . $motTestType);
        }
    }

    /**
     * @param MotTestDto  $motTest
     * @param string $motTestType
     * @param        $complaintRef
     *
     * @return array
     */
    protected function preparePostData(MotTestDto $motTest, $motTestType, $complaintRef)
    {
        /** @var \DvsaCommon\Dto\Vehicle\VehicleDto $vehicle */
        $vehicle = $motTest->getVehicle();

        $postData = [
            'vehicleId'               => $vehicle->getId(),
            'vehicleTestingStationId' => $motTest->getVehicleTestingStation()['id'],
            'primaryColour'           => $vehicle->getColour()->getCode(),
            'secondaryColour'         => $vehicle->getColourSecondary()->getCode(),
            'hasRegistration'         => $motTest->getHasRegistration(),
            'motTestType'             => $motTestType,
            'motTestNumberOriginal'   => $motTest->getMotTestNumber(),
            'complaintRef'            => $complaintRef,
            'fuelTypeId'              => $vehicle->getFuelType()->getCode(),
            'vehicleClassCode'        => $vehicle->getClassCode(),
        ];

        return $postData;
    }
}
