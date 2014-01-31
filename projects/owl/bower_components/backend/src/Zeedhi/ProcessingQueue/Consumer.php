<?php
namespace Zeedhi\ProcessingQueue;

use Symfony\Bundle\FrameworkBundle\Tests\Service;
use Zeedhi\Application;

include_once __DIR__."/../../../../projects/pedidos/public/bootstrap.php";
include_once __DIR__."/../../../libs/Stomp/Stomp.php";
/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 19/08/13
 * Time: 12:09
 * To change this template use File | Settings | File Templates.
 */


abstract class Consumer
{
    private $stompUrl;
    private $queueName;
    private $connection;
    private $transactionId;
    private $serviceUrl;

    const SECONDS = 1;

    public function __construct($stompUrl, $queue, $serviceUrl)
    {
        $this->stompUrl   = $stompUrl;
        $this->queueName  = $queue;
        $this->serviceUrl = $serviceUrl;
        $this->connection = new \Stomp($this->stompUrl);
        $this->connection->setReadTimeout(1);
        $this->connection->clientId = $this->getConsumerId();
        $this->transactionId = uniqid();
    }

    public function consume()
    {
        $this->connection->connect();
        $this->connection->subscribe($this->queueName);  
        $this->execute();
        $this->connection->unsubscribe($this->queueName);
        $this->connection->disconnect();
    }

    public function execute()
    {
        while(true) {
            try {
                sleep(self::SECONDS);
                while ($frame = $this->connection->readFrame()) {
                    $this->connection->begin($this->transactionId);
                    $this->callService($frame->body);die("\nfim");
                    $this->connection->ack($frame);
                    $this->connection->commit($this->transactionId);
                }
            } catch (\Exception $e) {
                $this->connection->abort($this->transactionId);
                throw $e;
            }
        }
    }

    public function getServiceUrl()
    {
        return $this->serviceUrl;
    }

    /**
     * Return consumer identifier.
     *
     * @return String
     */
    public abstract function getConsumerId();

    /**
     * This method must execute what was meant as the service queued.
     *
     * @param $body String Content sent by the producer.
     * @return mixed
     */
    public abstract function callService($body);

}
