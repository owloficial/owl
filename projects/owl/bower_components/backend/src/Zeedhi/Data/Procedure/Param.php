<?php

namespace Zeedhi\Data\Procedure;

class Param 
{
	const PARAM_TYPE_NULL = 0;
    const PARAM_TYPE_INT = 1;
    const PARAM_TYPE_STR = 2;
    const PARAM_TYPE_LOB = 3;
    const PARAM_TYPE_STMT = 4;
    const PARAM_TYPE_BOOL = 5;
    //const PARAM_INPUT_OUTPUT = -2147483648;

    const PARAM_INPUT = 'I';
    const PARAM_OUTPUT = 'O';
    const PARAM_INPUT_OUTPUT = 'IO';

    private $_in_out;
    private $_length;
    private $_name;
    private $_type;
    private $_value;

    public function __construct($name, $in_out, $value = null, $type = null, $length = null) {
        $this->_name = $name;
        $this->_in_out = $in_out;
        $this->_type = $type;
        if ($this->isOutput() && $length === null) {
            throw new \Exception("All output parameters must have a length value.");
        }

        $this->_length = $length;
        $this->_value = $value;
    }

    public function getInOut() {
        return $this->_in_out;
    }

    public function getLength() {
        return $this->_length;
    }

    public function getName() {
        return $this->_name;
    }

    public function getParamAlias() {
        return ":".$this->_name;
    }

    public function getType() {
        if ($this->isInput() && $this->isOutput()) {
            // "To return an INOUT parameter from a stored procedure, use the bitwise OR operator to set the
            // Doctrine_Core::PARAM_INPUT_OUTPUT bits for the data_type parameter."
            // @see Doctrine_Connection_Statement::bindParam()
            return $this->_type | Doctrine_Core::PARAM_INPUT_OUTPUT;
        }

        return $this->_type;
    }

    public function getValue() {
        return $this->_value;
    }

    public function getPreparedValue() {
        if(!$this->isInput()) return str_pad("0", $this->_length, "0");
        if($this->_value === null) return str_pad('NULL', $this->_length, ' ');
        if($this->_type === self::PARAM_TYPE_INT) {
            if($this->_value < 0) {
                $value = "-".str_pad("0", $this->_length-strlen((string)$this->_value), "0").(-1*$this->_value);
            } else {
                $value = str_pad("0", $this->_length-strlen((string)$this->_value), "0").$this->_value;
            }
        } else {
            $value = str_pad($this->_value, $this->_length, " ");
        }

        return $value;
    }

    public function setValue($value) {
        $this->_value = $value;
    }

    public function isInput() {
        // The 'strpos' return the integer value of occurrence, sometimes will be 0(zero), so we need to compare if
        // return value is 'false', that is equivalent with not found, and return the negation of comparison result.
        return !(strpos($this->_in_out, self::PARAM_INPUT)===false);
    }

    public function isOutput() {
        // The 'strpos' return the integer value of occurrence, sometimes will be 0(zero), so we need to compare if
        // return value is 'false', that is equivalent with not found, and return the negation of comparison result.
        return !(strpos($this->_in_out, self::PARAM_OUTPUT)===false);
    }

}