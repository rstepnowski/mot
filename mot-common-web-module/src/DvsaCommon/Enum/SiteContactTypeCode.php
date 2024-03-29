<?php

namespace DvsaCommon\Enum;

/**
 * Enum class generated from the 'site_contact_type' table
 *
 * DO NOT EDIT! -- THIS CLASS IS GENERATED BY mot-common-web-module/generate_enums.php
 * @codeCoverageIgnore
 */
class SiteContactTypeCode
{
    const CORRESPONDENCE = 'CORR';
    const BUSINESS = 'BUS';

    /**
     * @return array of values for the type SiteContactTypeCode
     */
    public static function getAll()
    {
        return [
            self::CORRESPONDENCE,
            self::BUSINESS,
        ];
    }

    /**
     * @param mixed $key a candidate SiteContactTypeCode value
     *
     * @return true if $key is in the list of known values for the type
     */
    public static function exists($key)
    {
        return in_array($key, self::getAll(), true);
    }
}
