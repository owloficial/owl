<?php
namespace Zeedhi\Router\Listener;

/**
 *
 * @author tuliopinto
 */
interface ExceptionListener {

    public function beforeHandleException($e);

}