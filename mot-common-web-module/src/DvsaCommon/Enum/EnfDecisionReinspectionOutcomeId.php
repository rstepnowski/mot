<?php

namespace DvsaCommon\Enum;

/**
 * Enum class generated from the 'enforcement_decision_reinspection_outcome_lookup' table
 *
 * DO NOT EDIT! -- THIS CLASS IS GENERATED BY mot-common-web-module/generate_enums.php
 * @codeCoverageIgnore
 */
class EnfDecisionReinspectionOutcomeId
{
    const AGREED_FULLY_WITH_TEST_RESULT = 1;
    const RESULT_CORRECT_BUT_ADVISORY_WARRANTED = 2;
    const RESULT_INCORRECT = 3;
    const OTHER_ENTER_DETAILS_IN_SECTION_C = 4;

    /**
     * @return array of values for the type EnfDecisionReinspectionOutcomeId
     */
    public static function getAll()
    {
        return [
            self::AGREED_FULLY_WITH_TEST_RESULT,
            self::RESULT_CORRECT_BUT_ADVISORY_WARRANTED,
            self::RESULT_INCORRECT,
            self::OTHER_ENTER_DETAILS_IN_SECTION_C,
        ];
    }

    /**
     * @param mixed $key a candidate EnfDecisionReinspectionOutcomeId value
     *
     * @return true if $key is in the list of known values for the type
     */
    public static function exists($key)
    {
        return in_array($key, self::getAll(), true);
    }
}
