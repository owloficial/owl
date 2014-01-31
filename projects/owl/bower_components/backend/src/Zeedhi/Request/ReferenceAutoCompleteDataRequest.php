<?php
namespace Zeedhi\Request;

/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 11/07/13
 * Time: 11:57
 * To change this template use File | Settings | File Templates.
 */
class ReferenceAutoCompleteDataRequest extends \Zeedhi\Request\Request
{

    private $value;
    private $in;

    public function setIn($in)
    {
        $this->in = $in;
    }

    public function getIn()
    {
        return $this->in;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
