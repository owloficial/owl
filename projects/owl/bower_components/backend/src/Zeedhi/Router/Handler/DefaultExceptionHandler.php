<?php
namespace Zeedhi\Router\Handler;

/**
 * Description of RuntimeExceptionHandler
 *
 * @author tuliopinto
 */
class DefaultExceptionHandler implements ExceptionHandler{

    public function handleException(\Exception $exception) {
        $response = new \Zeedhi\Request\Response\SimpleMessage();
        $response->setMessage("[Exception] " . $exception->getMessage());
        return $response->toJson();
    }

}