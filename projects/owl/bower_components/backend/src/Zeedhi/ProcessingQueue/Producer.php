<?php
namespace Zeedhi\ProcessingQueue;

include_once __DIR__."/../../../libs/Stomp/Stomp.php";
/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 19/08/13
 * Time: 12:09
 * To change this template use File | Settings | File Templates.
 */



class Producer
{
    private $url;
    private $queueName;
    private $persistent;

    private $connection;

    public function __construct($url, $queue, $persistent = false)
    {
        $this->url        = $url;
        $this->queueName  = $queue;
        $this->persistent = $persistent;

        $this->connection = new \Stomp($this->url);
    }

    public function send($msg)
    {
        $this->connection->connect($this->url);
        $headers = $this->getHeaders();
        $this->connection->send($this->queueName, $msg, $headers);
        $this->connection->disconnect();

    }

    private function getHeaders()
    {
        $headers = array();
        if ($this->persistent) {
            $headers["persistent"] = "true";
        }

        return $headers;
    }

}