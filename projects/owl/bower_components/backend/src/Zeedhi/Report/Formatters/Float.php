<?php
namespace Zeedhi\Report\Formatters;

class Float extends \Zeedhi\Report\Formatters\Formatter {

    /**
     * Apply formatter method.
     * 
     * @param string $text
     * @param array $params
     * 
     * @return string
     */
    public function apply($text, $params = array()) {
        return '<div style="text-align: right; width: 100%;">'.number_format($text , 2, ',', '.').'</div>';
    }

}