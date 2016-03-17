<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Dvsa\Mot\Behat\Support\Api\AuthorisedExaminer;
use Dvsa\Mot\Behat\Support\Helper\TestSupportHelper;
use Dvsa\Mot\Behat\Support\Response;
use PHPUnit_Framework_Assert as PHPUnit;
use TestSupport\Helper\TestDataResponseHelper;

class AuthorisedExaminerContext implements Context
{
    const AUTHORISED_EXAMINER_NUMBER = 'AE3412';

    /**
     * @var AuthorisedExaminer
     */
    private $authorisedExaminer;

    /**
     * @var TestSupportHelper
     */
    private $testSupportHelper;

    /**
     * @var SessionContext
     */
    private $sessionContext;

    /**
     * @var VtsContext
     */
    private $vtsContext;

    /**
     * @var Response
     */
    private $examinerDetailsResponse;

    /**
     * @var Response
     */
    private $removeAeResponse;

    /** @var  string */
    private $siteNrForLinking;

    /** @var  integer */
    private $aeIdForLinking;

    /**
     * @var array
     */
    private $createdAuthorisedExaminers = [];

    /**
     * @param AuthorisedExaminer $authorisedExaminer
     * @param TestSupportHelper $testSupportHelper
     */
    public function __construct(
        AuthorisedExaminer $authorisedExaminer,
        TestSupportHelper $testSupportHelper
    ) {
        $this->authorisedExaminer = $authorisedExaminer;
        $this->testSupportHelper = $testSupportHelper;
    }

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $this->sessionContext = $scope->getEnvironment()->getContext(SessionContext::class);
        $this->vtsContext = $scope->getEnvironment()->getContext(VtsContext::class);
    }

    /**
     * @When I search for an existing Authorised Examiner
     * @When I search for an existing Authorised Examiner by their number
     */
    public function iSearchForAnAuthorisedExaminer()
    {
        $this->examinerDetailsResponse = $this->authorisedExaminer->search(
            $this->sessionContext->getCurrentAccessToken(), self::AUTHORISED_EXAMINER_NUMBER
        );
    }

    /**
     * @When I attempt to obtain details of an Authorised Examiner
     */
    public function iAttemptToObtainDetailsOfAnAuthorisedExaminer()
    {
        $this->examinerDetailsResponse = $this->authorisedExaminer->getAuthorisedExaminerDetails(
            $this->sessionContext->getCurrentAccessTokenOrNull(),
            3
        );
    }

    /**
     * @Then I will not see the Authorised Examiner details
     */
    public function iWillNotSeeTheAuthorisedExaminerDetails()
    {
        PHPUnit::assertEquals(401, $this->examinerDetailsResponse->getStatusCode(), 'Incorrect status code returned.');
        PHPUnit::assertArrayNotHasKey(
            'data',
            $this->examinerDetailsResponse->getBody()->toArray(), 'Data key exists in response body.'
        );
    }

    /**
     * @When I search for an Invalid Authorised Examiner
     */
    public function iSearchForAnInvalidAuthorisedExaminer()
    {
        $this->examinerDetailsResponse = $this->authorisedExaminer->search(
            $this->sessionContext->getCurrentAccessToken(), "abcdefghijklmnopqrstuvwxyz"
        );
    }

    /**
     * @Then I will see the Authorised Examiner's details
     */
    public function theAuthorisedExaminerDetailsAreReturned()
    {
        $aeNumber = $this->examinerDetailsResponse->getBody()['data']['authorisedExaminerAuthorisation']['authorisedExaminerRef'];

        PHPUnit::assertSame($aeNumber, self::AUTHORISED_EXAMINER_NUMBER);
    }

    /**
     * @Then I am informed that Authorised Examiner does not exist
     */
    public function iAmInformedThatAuthorisedExaminerDoesNotExist()
    {
        PHPUnit::assertEquals($this->examinerDetailsResponse->getStatusCode(), 404);

        $body = $this->examinerDetailsResponse->getBody();
        PHPUnit::assertArrayHasKey('errors', $body);
        PHPUnit::assertStringEndsWith('not found', $body['errors'][0]['message']);
    }

    /**
     * @Then the Authorised Examiner record contains Data Disclosure data
     */
    public function theAuthorisedExaminerRecordContainsDataDisclosureData()
    {
        $dataDisclosure
            = $this->examinerDetailsResponse->getBody()['data']['authorisedExaminerAuthorisation']['dataDisclosure'];
        PHPUnit::assertContains($dataDisclosure, 'TODO');
    }

    /**
     * @Then I should be able to create a new Authorised Examiner
     */
    public function iCreateANewAuthorisedExaminer()
    {
        $authorisedExaminerResponse = $this->authorisedExaminer->createAuthorisedExaminer(
            $this->sessionContext->getCurrentAccessToken()
        );

        PHPUnit::assertEquals(200, $authorisedExaminerResponse->getStatusCode());
        $this->examinerDetailsResponse = $authorisedExaminerResponse;
    }

    /**
     * @When I attempt to remove an Authorised Examiner
     */
    public function iAttemptToRemoveAnAuthorisedExaminer()
    {
        $this->removeAeResponse = $this->authorisedExaminer->removeAuthorisedExaminer(
            $this->sessionContext->getCurrentAccessToken()
        );
        return $this->removeAeResponse;
    }

    /**
     * @Then the attempt will be forbidden
     */
    public function theAttemptWillBeForbidden()
    {
        PHPUnit::assertThat($this->removeAeResponse->getStatusCode(), PHPUnit::equalTo(403));
    }

    /**
     * @Then /^I should be able to approve this Authorised Examiner$/
     */
    public function approveAnAuthorisedExaminer()
    {
        $authorisedExaminerResponse = $this->authorisedExaminer->updateStatusAuthorisedExaminer(
            $this->sessionContext->getCurrentAccessToken(),
            $this->examinerDetailsResponse->getBody()['data']['id'],
            \DvsaCommon\Enum\AuthorisationForAuthorisedExaminerStatusCode::APPROVED
        );

        PHPUnit::assertEquals(200, $authorisedExaminerResponse->getStatusCode());
    }

    /**
     * @param int $slots
     * @param string $name
     * @return array
     */
    public function createAE($slots = 1001, $name = "default")
    {
        if (!empty($this->createdAuthorisedExaminers[$name])) {
            return $this->createdAuthorisedExaminers[$name];
        }

        $data = [
            "slots" => $slots,
            "requestor" => [
                "username" => "areaoffice1user",
                "password" => "Password1"
            ]
        ];

        $response = $this->testSupportHelper->getAeService()->create($data);

        $this->createdAuthorisedExaminers[$name] = [
            "id" => $response->data["id"],
            "aeRef" => $response->data["aeRef"],
            "aeName" => $response->data["aeName"],
        ];

        return $this->createdAuthorisedExaminers[$name];
    }

    public function iAttemptsToCreateAEAs($username, $password, $name = "default")
    {
        if (!empty($this->createdAuthorisedExaminers[$name])) {
            return $this->createdAuthorisedExaminers[$name];
        }

        $data = [
            "slots" => 1001,
            "requestor" => [
                "username" => $username,
                "password" => $password
            ]
        ];

        $response = $this->testSupportHelper->getAeService()->create($data);

        $this->createdAuthorisedExaminers[$name] = [
            "id" => $response->data["id"],
            "aeRef" => $response->data["aeRef"],
            "aeName" => $response->data["aeName"],
            "message" => $response->data["message"]
        ];

        return $this->createdAuthorisedExaminers[$name];
    }

    /**
     * @return array
     */
    public function getAe($name = "default")
    {
        if(array_key_exists($name, $this->createdAuthorisedExaminers)) {
            return $this->createdAuthorisedExaminers[$name];
        }

        return null;
    }

    /**
     * @Then /^I should be able to create a site for linking$/
     */
    public function iShouldBeAbleToCreateASiteForLinking()
    {
        $this->vtsContext->createSite();
        $site = $this->vtsContext->getSite();
        $this->siteNrForLinking  = $site['siteNumber'];

        $this->aeIdForLinking = $this->examinerDetailsResponse->getBody()['data'];
        // TODO this should be separate step 'I set AE status to Approved' and done by call to API
        $this->testSupportHelper->getAeService()->setSiteAEStatusApproved($this->aeIdForLinking['aeRef']);
    }

    /**
     * @Then /^I should be able to link the new AE and site together$/
     */
    public function iShouldBeAbleToLinkTheNewAEAndSiteTogether()
    {
        $linkResponse = $this->authorisedExaminer->linkAuthorisedExaminerWithSite(
            $this->sessionContext->getCurrentAccessToken(),
            $this->aeIdForLinking['id'],
            $this->siteNrForLinking
        );
        PHPUnit::assertEquals(200, $linkResponse->getStatusCode());
    }

    public function iLinkVtsToAe($aeId, $siteNumber, $name = "default")
    {
        $link = $this->authorisedExaminer->linkAuthorisedExaminerWithSite(
            $this->sessionContext->getCurrentAccessToken(), $aeId, $siteNumber
        );
        $linkId = $link->getBody()["data"]["id"];

        $this->createdAuthorisedExaminers[$name]["vtsLinks"][]=$linkId;
    }

    public function iUnlinkVtsFromAe($aeId, $linkId)
    {
        $unlink = $this->authorisedExaminer->unlinkSiteFromAuthorisedExaminer(
            $this->sessionContext->getCurrentAccessToken(), $aeId, $linkId
        );

        return $unlink;
    }

    public function iUnlinkEveryVtsFromAe($name = "default")
    {
        $aeId = $this->createdAuthorisedExaminers[$name]["id"];

        if(array_key_exists("vtsLinks", $this->createdAuthorisedExaminers[$name])) {
            foreach($this->createdAuthorisedExaminers[$name]["vtsLinks"] as $linkId) {
                $this->iUnlinkVtsFromAe($aeId, $linkId);
            }
        }
    }

    public function iGetTestLogs($name = "default")
    {
        $this->createdAuthorisedExaminers[$name]["testLogs"] = $this->authorisedExaminer->getTodaysTestLogs(
            $this->sessionContext->getCurrentAccessToken(), $this->createdAuthorisedExaminers[$name]["id"]
        )->getBody()["data"];

        return $this->createdAuthorisedExaminers[$name]["testLogs"];
    }
}
