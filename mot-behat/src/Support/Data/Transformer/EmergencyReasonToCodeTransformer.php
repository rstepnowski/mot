<?php
namespace Dvsa\Mot\Behat\Support\Data\Transformer;

use Dvsa\Mot\Behat\Support\Scope\BeforeBehatScenarioScope;
use DvsaCommon\Enum\EmergencyReasonCode;

trait EmergencyReasonToCodeTransformer
{
    /**
     * @Transform :emergencyCode
     */
    public function castEmergencyReasonToCode($emergencyReason)
    {
        if (BeforeBehatScenarioScope::isTransformerDisabled()) {
            return $emergencyReason;
        }

        switch ($emergencyReason) {
            case "system outage":
                return EmergencyReasonCode::SYSTEM_OUTAGE;
            case "communication problem":
                return EmergencyReasonCode::COMMUNICATION_PROBLEM;
            case "payment issue":
                return EmergencyReasonCode::PAYMENT_ISSUE;
            case "other":
                return EmergencyReasonCode::OTHER;
            default:
                throw new \InvalidArgumentException(sprintf("Cannot cast emergency reason '%s' to code.", $emergencyReason));
        }
    }
}
