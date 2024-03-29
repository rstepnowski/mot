<?php

namespace DvsaMotApi\Service\RfrValidator;

/**
 * Class BaseResultValidator
 *
 * @package DvsaMotApi\Service\RfrValidator
 */
class BaseResultValidator extends BaseValidator
{
    /**
     * @var null
     */
    protected $calculatedScore = null;

    /**
     * @var null
     */
    protected $values = null;

    /**
     * @var null
     */
    protected $error = null;

    public function __construct($values, $calculatedScore)
    {
        $this->values = $values;
        $this->calculatedScore = $calculatedScore;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return false;
    }

    /**
     * @param null $error
     *
     * @return BaseValidator
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param null $values
     *
     * @return BaseValidator
     */
    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * @return null
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param null $calculatedScore
     *
     * @return BaseResultValidator
     */
    public function setCalculatedScore($calculatedScore)
    {
        $this->calculatedScore = $calculatedScore;

        return $this;
    }

    /**
     * @return null
     */
    public function getCalculatedScore()
    {
        return $this->calculatedScore;
    }
}
