<?php
namespace Zeedhi\DTO;

class Response {

    const STATUS_SUCCESS = 'S';
    const STATUS_ERROR = 'E';

    /** @var string */
    protected $status;
    /** @var \Zeedhi\DTO\Response\Message[] */
    protected $messages;
    /** @var \Zeedhi\DTO\Response\Error */
    protected $error;
    /** @var  \Zeedhi\DTO\Response\DataSet[] */
    protected $dataSets;
    /** @var  \Zeedhi\DTO\Response\Method[] */
    protected $methods;

    function __construct()
    {
        $this->status = self::STATUS_SUCCESS;
        $this->methods = array();
        $this->messages = array();
        $this->dataSets = array();
    }

    /**
     * @return \Zeedhi\DTO\Response\Dataset[]
     */
    public function getDataSets()
    {
        return $this->dataSets;
    }

    public function setDataSets($dataSets)
    {
        $this->dataSets = $dataSets;
    }

    /**
     * @return \Zeedhi\DTO\Response\Error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return \Zeedhi\DTO\Response\Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return \Zeedhi\DTO\Response\Method[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param \Zeedhi\DTO\Response\Error $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function addMessage(Response\Message $message) {
        $this->messages[] = $message;
    }

    public function addDataSet(Response\DataSet $dataSet) {
        //@todo verify to index by "widget_id" //$this->dataSets[$dataSet->getId()] = $dataSet;
        $this->dataSets[] = $dataSet;
    }

    public function addMethod(Response\Method $method) {
        $this->methods[] = $method;
    }
}