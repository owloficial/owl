<?php
namespace Zeedhi\Router\Handler;

/**
 *
 * @author tuliopinto
 */
interface ExceptionHandler {

    /**
     * 
     * @return $response
     */
    public function handleException(\Exception $exception);

}