<?php

namespace DvsaCommon\Constants;

/**
 * Couple of RFR id
 */
interface ReasonForRejection
{
    const ITEM_NOT_TESTED_SELECTOR_ID = 5800;
    const ITEM_NOT_TESTED_BRAKE_PERFORMANCE_RFR_ID = 8566;

    const CLASS_12_BRAKE_PERFORMANCE_NOT_TESTED_RFR_ID = 10101;
    const CLASS_12_HEADLAMP_AIM_NOT_TESTED_RFR_ID = 10154;

    const CLASS_3457_BRAKE_PERFORMANCE_NOT_TESTED_RFR_ID = 10102;
    const CLASS_3457_EMISSIONS_NOT_TESTED_RFR_ID = 10152;
    const CLASS_3457_HEADLAMP_AIM_NOT_TESTED_RFR_ID = 10153;

    const MAX_DESCRIPTION_LENGTH = 255;
    const MAX_USER_COMMENT_LENGTH = 250;

    const AUDIENCE_TESTER_CODE = 't';
    const AUDIENCE_VEHICLE_EXAMINER_CODE = 'v';

    const BRAKE_PERFORMANCE_NOT_TESTED_RFR_IDS = [
        self::ITEM_NOT_TESTED_BRAKE_PERFORMANCE_RFR_ID,
        self::CLASS_12_BRAKE_PERFORMANCE_NOT_TESTED_RFR_ID,
        self::CLASS_3457_BRAKE_PERFORMANCE_NOT_TESTED_RFR_ID,
    ];
}
