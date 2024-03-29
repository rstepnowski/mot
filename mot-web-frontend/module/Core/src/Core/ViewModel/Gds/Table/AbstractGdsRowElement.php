<?php

namespace Core\ViewModel\Gds\Table;

class AbstractGdsRowElement implements GdsRowFlowInterface
{
    private $parentRow;

    public function __construct(GdsRow $parentRow)
    {
        $this->parentRow = $parentRow;
    }

    public function newRow($htmlId = null)
    {
        return $this->parentRow->newRow($htmlId = null);
    }

    public function getRow($index)
    {
        return $this->parentRow->getRow($index);
    }

    public function getRows()
    {
        return $this->parentRow->getRows();
    }

    public function getHtmlId()
    {
        return $this->parentRow->getHtmlId();
    }

    public function isHtmlClassSet()
    {
        return $this->parentRow->isHtmlClassSet();
    }

    public function getHtmlClass()
    {
        return $this->parentRow->getHtmlClass();
    }

    public function setLabel($content, $escape = true)
    {
        return $this->parentRow->setLabel($content, $escape);
    }

    public function getLabel()
    {
        return $this->parentRow->getLabel();
    }

    public function addActionLink($text, $url, $tooltip = ' ', $id = '')
    {
        return $this->parentRow->addActionLink($text, $url, $tooltip, $id);
    }

    public function getActionLink()
    {
        return $this->parentRow->getActionLink();
    }

    public function hasActionLink()
    {
        return $this->parentRow->hasActionLink();
    }

    public function setValue($content, $escape = true)
    {
        return $this->parentRow->setValue($content, $escape);
    }

    public function getValue()
    {
        return $this->parentRow->getValue();
    }

    public function setValueMetaData($content, $escape = true)
    {
        return $this->parentRow->setValueMetaData($content, $escape);
    }

    public function getValueMetaData()
    {
        return $this->parentRow->getValueMetaData();
    }
}
