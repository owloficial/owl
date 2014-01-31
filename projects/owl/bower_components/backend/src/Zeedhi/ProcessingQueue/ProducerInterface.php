<?php
namespace Zeedhi\ProcessingQueue;

use Zeedhi\Application;

class ProducerInterface
{

    public function send(\Zeedhi\Request\EventRequestDataSet $request, \Zeedhi\DTO\Response $response) 
    {
        $syncConfig = Application::getInstance()->getSyncConfig();

        try {
            $dataSet  = json_encode($request->getDataSet());
            $producer = new Producer($syncConfig["url"], $syncConfig["service"], $syncConfig["is_persistent"]);
            $producer->send($dataSet);
        } catch(\Exception $e) {
            throw $e;
        }
    }
    
}