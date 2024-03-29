<?php
namespace DvsaMotApiTest\Service\RfrValidator;

use PHPUnit_Framework_TestCase;
use \DvsaMotApi\Service\RfrValidator\BaseValidator;
use \DvsaMotApi\Service\RfrValidator\CheckDecisionsForCategoryNotApplicable;

/**
 * Class CheckDecisionsForCategoryNotApplicableTest
 *
 * @package DvsaMotApiTest\Service\RfrValidator
 */
class CheckDecisionsForCategoryNotApplicableTest extends AbstractValidatorTest
{
    /**
     * @param $mappedRfrId
     * @param $fixture
     *
     * @return CheckDecisionsForCategoryNotApplicable|mixed
     */
    protected function getValidator($mappedRfrId, $fixture)
    {
        return new CheckDecisionsForCategoryNotApplicable($mappedRfrId, $fixture);
    }

    /**
     * Get the name for this fixture to appear in generated tests
     *
     * @return array|string
     */
    public function getFixtureName()
    {
        return "Check Decisions For Category Not Applicable";
    }

    /**
     * @return array
     */
    public function getFixtures()
    {
        $fixtures = array();
        $fixtures[$this->validMappedRfrIds[0]] = [
            "rfrId" => $this->validRfrIds[0],
            "score" => 1,
            "decision" => BaseValidator::DEFECT_PLEASE_SELECT,
            "category" => BaseValidator::CATEGORY_NOT_APPLICABLE,
            "justification" => 'irrelevant for this test',
            "error" => 0,
            "message" => "",
            "fitnesse" => 0
        ];
        $fixtures[$this->validMappedRfrIds[1]] = [
            "rfrId" => $this->validRfrIds[1],
            "score" => 1,
            "decision" => BaseValidator::DEFECT_NOT_APPLICABLE,
            "category" => BaseValidator::CATEGORY_NOT_APPLICABLE,
            "justification" => 'irrelevant for this test',
            "error" => 0,
            "message" => "",
            "fitnesse" => 1
        ];
        $fixtures[$this->validMappedRfrIds[2]] = [
            "rfrId" => $this->validRfrIds[2],
            "score" => 1,
            "decision" => BaseValidator::DEFECT_MISSED,
            "category" => BaseValidator::CATEGORY_NOT_APPLICABLE,
            "justification" => 'irrelevant for this test',
            "error" => 1,
            "message" => BaseValidator::INVALID_DEFECT_FOR_CATEGORY,
            "failedItem" => "decision",
            "fitnesse" => 1
        ];
        $fixtures[$this->validMappedRfrIds[3]] = [
            "rfrId" => $this->validRfrIds[3],
            "score" => 1,
            "decision" => BaseValidator::DEFECT_INCORRECT_DECISION,
            "category" => BaseValidator::CATEGORY_NOT_APPLICABLE,
            "justification" => 'irrelevant for this test',
            "error" => 0,
            "message" => "",
            "fitnesse" => 1
        ];
        return $fixtures;
    }
}
