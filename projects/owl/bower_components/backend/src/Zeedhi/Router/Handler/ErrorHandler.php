<?php
namespace Zeedhi\Router\Handler;

/**
 *
 * @author tuliopinto
 */
interface ErrorHandler {

    /**
     * 
     * @return $response
     */
    public function handleError($error);

}