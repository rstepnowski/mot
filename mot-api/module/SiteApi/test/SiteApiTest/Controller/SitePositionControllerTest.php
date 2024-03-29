<?php

namespace SiteApiTest\Controller;

use DvsaCommon\Enum\BusinessRoleStatusCode;
use DvsaCommon\Enum\SiteBusinessRoleCode;
use DvsaCommonApiTest\Controller\AbstractRestfulControllerTestCase;
use DvsaCommonApiTest\Transaction\TestTransactionExecutor;
use DvsaCommonTest\Bootstrap;
use DvsaCommonTest\TestUtils\ArgCapture;
use DvsaCommonTest\TestUtils\XMock;
use DvsaEntities\Entity\BusinessRoleStatus;
use DvsaEntities\Entity\Person;
use DvsaEntities\Entity\Site;
use DvsaEntities\Entity\SiteBusinessRole;
use DvsaEntities\Entity\SiteBusinessRoleMap;
use SiteApi\Controller\SitePositionController;
use SiteApi\Service\NominateRoleService;
use SiteApi\Service\SitePositionService;
use Zend\Stdlib\Parameters;

/**
 * Class SitePositionControllerTest
 *
 * @package SiteApiTest\Controller
 */
class SitePositionControllerTest extends AbstractRestfulControllerTestCase
{
    private $sitePositionServiceMock;
    private $organisationId = 1;
    private $nomineeId = 1;
    private $roleCode;
    private $nominateRoleServiceMock;

    protected function setUp()
    {
        $this->roleCode = SiteBusinessRoleCode::TESTER;
        $this->controller = new SitePositionController();
        $this->sitePositionServiceMock = $this->getSitePositionServiceMock();
        $this->nominateRoleServiceMock = $this->getNominateRoleServiceMock();
        $this->setupServiceManager();
        TestTransactionExecutor::inject($this->controller);
        parent::setUp();
    }

    public function testCreateWithValidDataCanBeAccessed()
    {
        //given
        $this->routeMatch->setParam('siteId', $this->organisationId);
        //when
        $result = $this->controller->create(
            [
                'nomineeId' => $this->nomineeId,
                'roleCode'  => SiteBusinessRoleCode::TESTER
            ]
        );
        //then
        $this->assertInstanceOf("Zend\View\Model\JsonModel", $result);
    }

    public function testDelete_givenRouteParams_shouldCallServiceProperly()
    {
        list($capSiteId, $capPositionId) = [ArgCapture::create(), ArgCapture::create()];
        list($inputSiteId, $inputPositionId) = [1, 5];

        $this->routeMatch->setParam("siteId", $inputSiteId)
            ->setParam("positionId", $inputPositionId);
        $this->request->setMethod("DELETE");

        $this->sitePositionServiceMock = $this->getSitePositionServiceMock();
        $this->sitePositionServiceMock->expects($this->atLeastOnce())
            ->method("remove")
            ->with($capSiteId(), $capPositionId());
        $this->setupServiceManager();

        $this->controller->dispatch($this->request);

        $this->assertEquals($inputSiteId, $capSiteId->get());
        $this->assertEquals($inputPositionId, $capPositionId->get());
    }

    private function getSitePositionServiceMock()
    {
        $organisationPositionServiceMock = XMock::of(SitePositionService::class);
        return $organisationPositionServiceMock;
    }

    private function getNominateRoleServiceMock()
    {
        $status = (new BusinessRoleStatus())->setCode(BusinessRoleStatusCode::ACTIVE);
        $orgPosition = new SiteBusinessRoleMap();
        $orgPosition->setPerson(new Person());
        $orgPosition->setSite(new Site());
        $orgPosition->setSiteBusinessRole((new SiteBusinessRole())->setCode(SiteBusinessRoleCode::TESTER));
        $orgPosition->setBusinessRoleStatus($status);
        $nominatedRoleServiceMock = XMock::of(NominateRoleService::class);

        $nominatedRoleServiceMock->expects($this->any())
            ->method('nominateRole')
            ->with($this->organisationId, $this->nomineeId, $this->roleCode)
            ->will($this->returnValue($orgPosition));

        return $nominatedRoleServiceMock;
    }

    private function setupServiceManager()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService(SitePositionService::class, $this->sitePositionServiceMock);
        $serviceManager->setService(NominateRoleService::class, $this->nominateRoleServiceMock);
        $this->controller->setServiceLocator($serviceManager);
    }
}
