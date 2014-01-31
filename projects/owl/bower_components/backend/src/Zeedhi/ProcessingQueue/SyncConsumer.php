<?php
/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 22/08/13
 * Time: 12:01
 * To change this template use File | Settings | File Templates.
 */
namespace Zeedhi\ProcessingQueue;

use Zeedhi\Application;
use Zeedhi\HTTP\Response;
use Zeedhi\Request\RequestFactory;

/**
 * Class SyncConsumer
 *
 * An activeMQ consumer, implemented to consume a queue of requests to a Zeedhi app.
 *
 * @package Zeedhi\ProcessingQueue
 */
class SyncConsumer extends Consumer
{
    const SERVICE_NAME = "serviceName";

    /**
     * Return consumer identifier.
     *
     * @return String
     */
    public function getConsumerId()
    {
        return "sync_client";
    }

    /**
     * This method must execute what was meant as the service queued.
     *
     * @param String $body
     * @return mixed
     */
    public function callService($body)
    {
        try {
            $serviceBaseUrl = Application::getInstance($this->getServiceUrl())->getBaseUri();
            $serviceAddress = Application::getInstance()->getSyncConfig();
            $serviceAddress = $serviceAddress["service_address"];
            $body = json_decode($body, true);
            $serviceUrl = $serviceAddress . $serviceBaseUrl . $body[self::SERVICE_NAME];
            $curl = curl_init($serviceUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($body));
            $curlResponse = curl_exec($curl);
            echo $curlResponse;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}