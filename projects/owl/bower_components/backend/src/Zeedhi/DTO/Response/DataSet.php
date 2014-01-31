<?php
namespace Zeedhi\DTO\Response;

class DataSet {
    //@todo implement
    /** @var string */
    protected $id;
    /** @var mixed */
    protected $content;

    function __construct($id, $content)
    {
        $this->content = $content;
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}