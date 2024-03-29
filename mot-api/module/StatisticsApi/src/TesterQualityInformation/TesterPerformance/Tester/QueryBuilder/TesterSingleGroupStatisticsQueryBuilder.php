<?php
namespace Dvsa\Mot\Api\StatisticsApi\TesterQualityInformation\TesterPerformance\Tester\QueryBuilder;

use Dvsa\Mot\Api\StatisticsApi\TesterQualityInformation\TesterPerformance\Common\QueryBuilder\TesterPerformanceQueryBuilder;

class TesterSingleGroupStatisticsQueryBuilder extends TesterPerformanceQueryBuilder
{
    protected $index = "USE INDEX (`ix_mot_test_current_person_id_started_date_completed_date`)";
    protected $where = "AND `person`.`id` = :testerId AND `class_group`.`code` = :groupCode";
    protected $selectFields = "vts.name AS siteName, ";
}
