<?php
namespace Zeedhi\Report\Formatters;

class MaxLength extends \Zeedhi\Report\Formatters\Formatter {

    /**
     * Apply formatter method.
     * 
     * @param string $text
     * @param array $params
     * 
     * @return string
     */
    public function apply($text, $params = array()) {
        $maxLength = $params[0];
        $sufix     = "...";

        if ($maxLength > 0 && strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength).$sufix;
        }

        return $text;
    }

}