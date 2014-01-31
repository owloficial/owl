<?php
namespace Zeedhi\DTO\Response;

class Method {

    /** @var string */
    protected $name;
    /** @var array */
    protected $parameters;

    function __construct($name, $parameters = array())
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}