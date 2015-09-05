<?php

namespace DvsaCommon\Enum;

/**
 * Enum class generated from the 'organisation_site_status' table
 *
 * DO NOT EDIT! -- THIS CLASS IS GENERATED BY mot-common-web-module/generate_enums.php
 * @codeCoverageIgnore
 */
class OrganisationSiteStatusName
{
    const UNKNOWN = 'Unknown';
    const APPLIED = 'Applied';
    const ACTIVE = 'Active';
    const WITHDRAWN = 'Withdrawn';
    const SURRENDERED = 'Surrendered';
    const REJECTED = 'Rejected';
    const SUSPENDED = 'Suspended';
    const RETRACTED = 'Retracted';
    const EXTINCT = 'Extinct';

    /**
     * @return array of values for the type OrganisationSiteStatusName
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
     * @param mixed $key a candidate OrganisationSiteStatusName value
     *
     * @return true if $key is in the list of known values for the type
     */
    public static function exists($key)
    {
        return in_array($key, self::getAll(), true);
    }
}
