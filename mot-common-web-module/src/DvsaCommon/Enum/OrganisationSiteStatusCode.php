<?php

namespace DvsaCommon\Enum;

/**
 * Enum class generated from the 'organisation_site_status' table
 *
 * DO NOT EDIT! -- THIS CLASS IS GENERATED BY mot-common-web-module/generate_enums.php
 * @codeCoverageIgnore
 */
class OrganisationSiteStatusCode
{
    const UNKNOWN = 'UNKN';
    const APPLIED = 'AP';
    const ACTIVE = 'AC';
    const WITHDRAWN = 'WD';
    const SURRENDERED = 'SR';
    const REJECTED = 'RJ';
    const SUSPENDED = 'SP';
    const RETRACTED = 'RE';
    const EXTINCT = 'EX';

    /**
     * @return array of values for the type OrganisationSiteStatusCode
     */
    public static function getAll()
    {
        return [
            self::UNKNOWN,
            self::APPLIED,
            self::ACTIVE,
            self::WITHDRAWN,
            self::SURRENDERED,
            self::REJECTED,
            self::SUSPENDED,
            self::RETRACTED,
            self::EXTINCT,
        ];
    }

    /**
     * @param mixed $key a candidate OrganisationSiteStatusCode value
     *
     * @return true if $key is in the list of known values for the type
     */
    public static function exists($key)
    {
        return in_array($key, self::getAll(), true);
    }
}
