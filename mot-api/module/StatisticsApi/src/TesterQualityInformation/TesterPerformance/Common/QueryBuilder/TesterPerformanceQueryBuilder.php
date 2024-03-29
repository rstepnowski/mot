<?php
namespace Dvsa\Mot\Api\StatisticsApi\TesterQualityInformation\TesterPerformance\Common\QueryBuilder;


class TesterPerformanceQueryBuilder
{
    protected $selectFields;
    protected $index;
    protected $join;
    protected $where;
    protected $groupBy;

    public function getSql()
    {
        return "SELECT {$this->selectFields} SUM(TIMESTAMPDIFF(SECOND, `test`.`started_date`, `test`.`completed_date`))  `totalTime`,
                  SUM(IF(`status`.`code` = :failedStatusCode, 1, 0))                          `failedCount`,
                  COUNT(*)                                                                    `totalCount`,
                  AVG(TIMESTAMPDIFF(MONTH,
                    COALESCE(`v`.`manufacture_date`,
                             `v_hist`.`manufacture_date`),
                    `test`.`completed_date`)) AS `averageVehicleAgeInMonths`
                FROM `mot_test_current` `test` {$this->index}
                  LEFT JOIN `vehicle` `v` ON (`test`.`vehicle_id` = `v`.`id` AND `test`.`vehicle_version` = `v`.`version`)
                  LEFT JOIN `vehicle_hist` `v_hist` ON (`test`.`vehicle_id` = `v_hist`.`id` AND `test`.`vehicle_version` = `v_hist`.`version`)
                  JOIN `person` `person` ON `person`.`id` = `test`.`person_id`
                  JOIN `mot_test_type` `type` ON `type`.`id` = `test`.`mot_test_type_id`
                  JOIN `mot_test_status` `status` ON `status`.`id` = `test`.`status_id`
                  JOIN `model_detail` `md` ON `md`.`id` = COALESCE (`v`.`model_detail_id`, `v_hist`.`model_detail_id`)
                  JOIN `vehicle_class` `class` ON `class`.`id` = `md`.`vehicle_class_id`
                  JOIN `vehicle_class_group` `class_group` ON `class_group`.`id` = `class`.`vehicle_class_group_id`
                  JOIN `site` `vts` ON `vts`.`id` = `test`.`site_id`
                  LEFT JOIN mot_test_emergency_reason mter ON mter.id = test.id
                  {$this->join}
                WHERE `test`.`completed_date` BETWEEN :startDate AND :endData
        -- the only tests we take into account are failures or non PRS passed ones
        AND (`status`.`code` = :failedStatusCode OR (`status`.`code` = :passStatusCode AND `test`.`prs_mot_test_id` IS NULL))
        AND (`type`.`code` = :normalTestCode OR `type`.`code` = :mysteryShopperTestCode)
        AND `emergency_log_id` IS NULL
        {$this->where}
        AND EXISTS
        (
            -- the site had to have an active association to the current AE during the time when the test was performed
                      SELECT * FROM `organisation_site_map` `associationToAe`
                      JOIN `organisation_site_status` `associationStatus` ON `associationStatus`.`id` = `associationToAe`.`status_id`
                      WHERE `associationToAe`.`site_id` = `vts`.`id`
        AND `associationToAe`.`organisation_id` = `vts`.`organisation_id`
        AND `associationStatus`.`code` NOT IN (:irrelevantAssociationCodes)
                      AND `associationToAe`.`start_date` <= `test`.`completed_date`
        AND
        (
            `associationToAe`.`end_date` >= `test`.`completed_date`
            OR `associationToAe`.`end_date` IS NULL)
        )
        {$this->groupBy}";
    }
}
