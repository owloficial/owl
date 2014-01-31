<?php
namespace Zeedhi\Router\Event;

/**
 * Description of AbstractEvent
 *
 * @author tuliopinto
 */
abstract class AbstractEvent {

    private $listeners = array();

    public function __construct() {}

    public function addListener($listener) {
        $this->listeners[] = $listener;
    }

}
