<?php
namespace Zeedhi\Router\Handler;

/**
 * Description of ServiceNotFound
 *
 * @author tuliopinto
 */
class ServiceNotFound implements NotFoundHandler{

    public function handleNotFound() {
        $response = new \Zeedhi\Request\Response\SimpleMessage();
        $response->setMessage("Service not found!");
        return $response->toJson();
    }

}