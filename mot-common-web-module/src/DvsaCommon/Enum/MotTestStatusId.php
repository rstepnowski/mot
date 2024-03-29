<?php

namespace DvsaCommon\Enum;

/**
 * Enum class generated from the 'mot_test_status' table
 *
 * DO NOT EDIT! -- THIS CLASS IS GENERATED BY mot-common-web-module/generate_enums.php
 * @codeCoverageIgnore
 */
class MotTestStatusId
{
    const ABANDONED = 1;
    const ABORTED = 2;
    const ABORTED_VE = 3;
    const ACTIVE = 4;
    const FAILED = 5;
    const PASSED = 6;
    const REFUSED = 7;

    /**
     * @return array of values for the type MotTestStatusId
     */
    public static function getAll()
    {
        return [
            self::ABANDONED,
            self::ABORTED,
            self::ABORTED_VE,
            self::ACTIVE,
            self::FAILED,
            self::PASSED,
            self::REFUSED,
        ];
    }

    /**
     * @param mixed $key a candidate MotTestStatusId value
     *
     * @return true if $key is in the list of known values for the type
     */
    public static function exists($key)
    {
        return in_array($key, self::getAll(), true);
    }
}
