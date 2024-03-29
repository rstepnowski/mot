<?php

namespace UserApiTest\Dashboard\Dto;

use Doctrine\Common\Collections\ArrayCollection;
use UserApi\Dashboard\Dto\MonthStats;

/**
 * Class MonthStatsTest
 *
 * @package UserApiTest\Dashboard\Dto
 */
class MonthStatsTest extends \PHPUnit_Framework_TestCase
{
    public function testDto()
    {
        $averageTime = 1;
        $failRate = 2;

        $stats = new MonthStats(
            $averageTime,
            $failRate
        );

        $return = $stats->toArray();
        $this->assertEquals($averageTime, $return['averageTime']);
        $this->assertEquals($failRate, $return['failRate']);
    }
}
