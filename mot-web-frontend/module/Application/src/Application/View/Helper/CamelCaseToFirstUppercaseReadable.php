<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Class.
 */
class CamelCaseToFirstUppercaseReadable extends CamelCaseToReadable
{
    public function __invoke($string = null)
    {
        $camelCaseReadable = parent::__invoke($string);

        if (!$camelCaseReadable) {
            return null;
        }

        return ucfirst(strtolower($camelCaseReadable));
    }
}
