<?php

namespace DvsaMotTest\Controller;

use DvsaCommon\Dto\MotTesting\MotTestOptionsDto;
use DvsaCommon\Enum\MotTestTypeCode;
use DvsaCommon\Factory\AutoWire\AutoWireableInterface;
use DvsaCommon\UrlBuilder\UrlBuilder;
use DvsaMotTest\Service\MotChecklistPdfService;
use DvsaMotTest\Presenter\MotTestOptionsPresenter;
use Zend\View\Model\ViewModel;

class MotTestOptionsController extends AbstractDvsaMotTestController implements AutoWireableInterface
{
    const ROUTE_MOT_TEST_OPTIONS = 'mot-test/options';

    const TEMPLATE_MOT_TEST_OPTIONS = 'dvsa-mot-test/mot-test/mot-test-options.phtml';

    const PAGE_TITLE_TEST = 'MOT test started';
    const PAGE_TITLE_RETEST = 'MOT retest started';

    const PAGE_SUB_TITLE_TRAINING = 'Training test';
    const PAGE_SUB_TITLE_TEST = 'MOT testing';

    protected $motChecklistPdfService;

    public function __construct(MotChecklistPdfService $motChecklistPdfService)
    {
        $this->motChecklistPdfService = $motChecklistPdfService;
    }

    public function motTestOptionsAction()
    {
        $motTestNumber = $this->params()->fromRoute('motTestNumber');

        $dto = MotTestOptionsDto::fromArray(
            $this->getRestClient()->get(UrlBuilder::motTestOptions($motTestNumber)->toString())['data']
        );

        $presenter = new MotTestOptionsPresenter($dto);
        $presenter->setMotTestNumber($motTestNumber);

        $pageTitle = self::PAGE_TITLE_TEST;

        if ($presenter->isMotTestRetest()) {
            $pageTitle = self::PAGE_TITLE_RETEST;
        }

        $this->layout()->setVariable('pageTitle', $pageTitle);

        if ($dto->getMotTestTypeDto()->getCode() === MotTestTypeCode::DEMONSTRATION_TEST_FOLLOWING_TRAINING) {
            $this->layout()->setVariable('pageSubTitle', self::PAGE_SUB_TITLE_TRAINING);
        } else {
            $this->layout()->setVariable('pageSubTitle', self::PAGE_SUB_TITLE_TEST);
        }

        $viewModel = new ViewModel(['presenter' => $presenter]);
        $viewModel->setTemplate(self::TEMPLATE_MOT_TEST_OPTIONS);

        return $viewModel;
    }

    public function motChecklistAction()
    {
        $motTestNumber = $this->params()->fromRoute('motTestNumber');
        $pdf = $this->motChecklistPdfService->getChecklistPdf($motTestNumber);
        $this->getResponse()
            ->setContent($pdf)
            ->getHeaders()->addHeaders([
                'Content-Length'        =>  strlen($pdf),
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'filename=Mot-checklist_' . $motTestNumber . '.pdf',
            ]);

        return $this->getResponse();
    }

}
