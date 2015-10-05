<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Dvsa\Mot\Behat\Support\Api\Certificate;
use Dvsa\Mot\Behat\Support\Api\MotTest;
use Dvsa\Mot\Behat\Support\Helper\TestSupportHelper;
use PHPUnit_Framework_Assert as PHPUnit;
use Smalot\PdfParser\Document;

class CertificateContext implements Context
{
    /**
     * @var Certificate
     */
    private $certificate;

    /**
     * @var MotTest
     */
    private $motTest;

    /**
     * @var array
     */
    private $motTests;

    /**
     * @var SessionContext
     */
    private $sessionContext;

    /**
     * @var MotTestContext
     */
    private $motTestContext;

    private $certificateResult;

    private $recentTestsCertificateDetails;
    /**
     * @var TestSupportHelper
     */
    private $testSupportHelper;

    /**
     * @param Certificate $certificate
     * @param MotTest $motTest
     * @param TestSupportHelper $testSupportHelper
     */
    public function __construct(Certificate $certificate, MotTest $motTest, TestSupportHelper $testSupportHelper)
    {
        $this->certificate = $certificate;
        $this->motTest = $motTest;
        $this->testSupportHelper = $testSupportHelper;
    }

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $this->sessionContext = $scope->getEnvironment()->getContext(SessionContext::class);
        $this->motTestContext = $scope->getEnvironment()->getContext(MotTestContext::class);
    }

    /**
     * @Then /^the certificate contains (.*) pages$/
     */
    public function theCertificateContains($noOfPages)
    {
        $pdf = $this->parsePdf($this->certificateResult);

        PHPUnit::assertEquals($noOfPages, count($pdf->getPages()), 'PDF does not contain correct number of pages');
    }

    /**
     * @Given /^requests the certificate$/
     */
    public function requestsTheCertificate()
    {
        $this->certificateResult = $this->certificate->requestCertificate(
            $this->motTestContext->getMotTestNumber(),
            $this->sessionContext->getCurrentAccessToken()
        );
    }

    /**
     * @Given /^the certificate contains the text (.*)$/
     */
    public function theCertificateContainsTheText($expectedText)
    {
        $pdf = $this->parsePdf($this->certificateResult);

        if ((bool)strpos($expectedText, '&&')) {
            $expectedText = explode('&&', $expectedText);
        }

        if (is_array($expectedText)) {
            foreach ($expectedText as $text) {
                $result = (bool)strpos($pdf->getText(), trim($text));
                PHPUnit::assertTrue($result, 'Could not find expected text in document - ' . $text);
            }
        } else {
            $result = (bool)strpos($pdf->getText(), $expectedText);
            PHPUnit::assertTrue($result, 'Could not find expected text in document - ' . $expectedText);
        }
    }

    /**
     * @When /^I retrieve recent tests certificate details in the VTS recent test was performed$/
     */
    public function iRetrieveRecentTestsCertificateDetails()
    {
        $token = $this->sessionContext->getCurrentAccessToken();
        $vtsId = $this->motTestContext->getMotTestData()['vehicleTestingStation']['id'];
        $this->recentTestsCertificateDetails = $this->motTest->getRecentTestsCertificateDetails($token, $vtsId)->getBody();
    }

    /**
     * @Then /^I can retrieve certificate details for the most recent test from the list$/
     */
    public function certificateDetailsForRecentTestAreAvailable()
    {
        $token = $this->sessionContext->getCurrentAccessToken();
        $certificateDetailsId = $this->recentTestsCertificateDetails['data']['items'][0]['id'];
        $certificateDetailsResponse = $this->motTest->getRecentTestCertificateDetails(
            $token,
            $certificateDetailsId
        );

        PHPUnit::assertEquals(
            200,
            $certificateDetailsResponse->getStatusCode(),
            "Recent test certificate details enpoint did not return 200 code"
        );
    }

    /**
     * @When /^I reprint an MOT Test Certificate$/
     */
    public function iReprintAnMOTTestCertificate()
    {
        $token = $this->sessionContext->getCurrentAccessToken();
        $this->motTest->getMOTCertificateDetails($token, '651157444199');

        $this->certificate->getDuplicateCertificate($token);
    }

    /**
     * @param mixed $certificate
     *
     * @return Document
     *
     * @throws \Exception
     */
    private function parsePdf($certificate)
    {
        try {
            $parser = new \Smalot\PdfParser\Parser();

            return $parser->parseContent($certificate);
        } catch (\Exception $ex) {
            throw new \Exception('Unable to parse the MOT certificate. ' . $ex->getMessage());
        }
    }

    /**
     * @When I fetch jasper document for test
     */
    public function iFetchJasperDocumentForTest()
    {
        $motTests = [];
        foreach($this->motTestContext->getMotTests() as $key => $motTest)
        {
            $document = $this->testSupportHelper->getDocumentService()->get($motTest['document'])[0];
            $motTest['document'] = $document;
            $motTests[]= $motTest;
        }
        $this->motTestContext->setMotTests($motTests);
    }

    /**
     * @Then document has only odometer readings from tests performed in past
     */
    public function documentHasOnlyOdometerReadingsFromTestsPerformedInPast()
    {
        $fullOdometerHistory = [];
        foreach($this->motTestContext->getMotTests() as $motTest) {
            $fullOdometerHistory[]= [
                'startedDate' => $motTest['startedDate'],
                'odometerReading' => $motTest['odometerReading']
            ];
        }

        foreach($this->motTestContext->getMotTests() as $motTest) {
            $documentContent = json_decode($motTest['document']['document_content'], true);
            $documentOdometerHistory = $documentContent['OdometerHistory'];
            $this->validateOdometerHistory($fullOdometerHistory, $documentOdometerHistory, $motTest['startedDate']);
        }
    }

    private function validateOdometerHistory($fullOdometerHistory, $documentOdometerHistory, $startedDate)
    {
        $expectedOdometerReadings = array_filter($fullOdometerHistory, function($value) use ($startedDate) {
            $motTestDate = new DateTime($startedDate);
            $odometerReadingDate = new DateTime($value['startedDate']);

            return $motTestDate >= $odometerReadingDate;
        });

        $expectedOdometerValues = [];
        foreach($expectedOdometerReadings as $reading) {
            $expectedOdometerValues[]= $reading['odometerReading']['value'] . ' ' . $reading['odometerReading']['unit'];
        }

        $documentOdometerValues = array_reduce(explode("\n", $documentOdometerHistory), function($result, $value) {
            preg_match('/: ([0-9].*)/', $value, $matches);
            $result[]=$matches[1];

            return $result;
        });

        PHPUnit::assertEquals($expectedOdometerValues, $documentOdometerValues);
    }

    /**
     * @Given print of created mot tests is issued
     * @Given print of migrated mot tests is issued
     */
    public function printOfCreatedMotTestsIsIssued()
    {
        foreach($this->motTestContext->getMotTests() as $motTest) {
            $this->certificate->requestCertificate($motTest['motTestNumber'], $this->sessionContext->getCurrentAccessToken());
        }

        $this->motTestContext->refreshMotTests();
    }


    /**
     * @Given jasper documents were not printed
     */
    public function removeJasperDocumentsForMotTests()
    {
        $motTests=[];
        foreach($this->motTestContext->getMotTests() as $motTest)
        {
            $this->testSupportHelper->getDocumentService()->delete($motTest['document']);
            unset($motTest['document']);
            $motTests[]=$motTest;
        }
        $this->motTestContext->setMotTests($motTests);
    }
}