<?php
namespace Zeedhi\Report\Formatters;

abstract class Formatter {

    /**
     * Apply formatter method.
     * 
     * @param string $text
     * @param array $params
     * 
     * @return string
     */
    public abstract function apply($text, $params = array());

}