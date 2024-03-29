<?php


namespace Core\Catalog\Vts;


class VtsType
{
    private $code;
    private $name;

    function __construct($code, $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getName()
    {
        return $this->name;
    }
}