<?php

namespace SiteTest\ViewModel;

use Core\ViewModel\Equipment\EquipmentViewModel;
use DvsaClient\Entity\SiteDailyOpeningHours;
use DvsaCommon\Date\DateTimeDisplayFormat;
use DvsaCommon\Date\Time;
use DvsaCommon\Dto\Equipment\EquipmentDto;
use DvsaCommon\Dto\Equipment\EquipmentModelDto;
use DvsaCommon\Dto\MotTesting\MotTestInProgressDto;
use DvsaCommon\Dto\Site\SiteTestingDailyScheduleDto;
use DvsaCommon\Dto\Site\VehicleTestingStationDto;
use DvsaCommon\Utility\ArrayUtils;
use DvsaCommonTest\TestUtils\XMock;
use Site\Authorization\VtsOverviewPagePermissions;
use Site\ViewModel\SiteViewModel;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Zend\Mvc\Controller\Plugin\Url;

/**
 * Class SiteViewModelTest
 * @package SiteTest\ViewModel
 */
class SiteViewModelTest extends \PHPUnit_Framework_TestCase
{
    const STATUS_MODEL = 'good';

    /**
     * @var VtsOverviewPagePermissions|MockObject
     */
    private $permissions;
    private $site;
    private $equipments;
    private $tests;
    private $model;
    /** @var Url */
    private $urlHelper;

    public function SetUp()
    {
        $this->site = (new VehicleTestingStationDto());
        $this->equipments = [(new EquipmentDto())->setModel((new EquipmentModelDto())->setStatus(self::STATUS_MODEL))];
        $this->tests = [(new MotTestInProgressDto())];
        $this->permissions = XMock::of(VtsOverviewPagePermissions::class);
        $this->model = [self::STATUS_MODEL => 'test'];
        $this->urlHelper = XMock::of(Url::class);
    }

    public function testGetterSetter()
    {
        $model = new SiteViewModel($this->site, $this->equipments, $this->tests, $this->permissions, $this->model, $this->urlHelper);

        $this->assertEquals($this->site, $model->getSite());
        $this->assertEquals($this->permissions, $model->getPermissions());
        $this->assertEquals('England', $model->getCountryToggle());

        $model = new SiteViewModel($this->site->setIsScottishBankHoliday(true), $this->equipments, $this->tests, $this->permissions, $this->model, $this->urlHelper);
        $this->assertEquals('Scotland', $model->getCountryToggle());
        $model = new SiteViewModel($this->site->setIsDualLanguage(true), $this->equipments, $this->tests, $this->permissions, $this->model, $this->urlHelper);
        $this->assertEquals('Wales', $model->getCountryToggle());

        $this->assertEquals(
            SiteDailyOpeningHours::$DAY_NAMES[1],
            $model->displayWeekday((new SiteTestingDailyScheduleDto())->setWeekday(1))
        );

        $this->assertEquals(
            'Closed',
            $model->displayOpeningHours(
                (new SiteTestingDailyScheduleDto())->setOpenTime(null)->setCloseTime(null)
            )
        );
    }
}
