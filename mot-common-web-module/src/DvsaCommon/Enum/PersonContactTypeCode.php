<?php

namespace DvsaCommon\Enum;

/**
 * Enum class generated from the 'person_contact_type' table
 *
 * DO NOT EDIT! -- THIS CLASS IS GENERATED BY mot-common-web-module/generate_enums.php
 * @codeCoverageIgnore
 */
class PersonContactTypeCode
{
    const PERSONAL = 'PRSNL';
    const WORK = 'WORK';

    /**
     * @return array of values for the type PersonContactTypeCode
     */
    public static function getAll()
    {
        return [
            self::PERSONAL,
            self::WORK,
        ];
    }

    /**
     * @param mixed $key a candidate PersonContactTypeCode value
     *
     * @return true if $key is in the list of known values for the type
     */
    public static function exists($key)
    {
        return in_array($key, self::getAll(), true);
    }
}
