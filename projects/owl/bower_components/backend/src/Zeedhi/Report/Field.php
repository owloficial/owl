<?php

namespace Zeedhi\Report;

class Field 
{
    public $name;
    public $label;
    public $value;
    public $formatterClass;
    public $formatterParams = array();

    public function setLabel($label) 
    {
        $this->label = $label;
    }

    public function getLabel() 
    {
        return $this->label;
    }

    public function setName($name) 
    {
        return $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setValue($value) 
    {
        return $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getFormatterClass() {
        return $this->formatterClass;
    }

    public function setFormatterClass($formatterClass) {
        $this->formatterClass = $formatterClass;
    }

    public function getFormatterParams() {
        return $this->formatterParams;
    }

    public function setFormatterParams($formatterParams) {
        $this->formatterParams = $formatterParams;
    }

}