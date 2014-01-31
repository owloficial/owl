<?php
namespace Zeedhi\Service;

/**
 * Description of Handler
 *
 * @author tulio
 */
class Handler implements \Respect\Rest\Routable {
    /**
    *
    *
    */
    private $securityManager;

    public function __construct(\Zeedhi\Security\SecurityManager $securityManager){
        $this->securityManager = $securityManager;
    }

    public function any($serviceName) {
        var_dump($serviceName);
//        $this->securityManager->afterInvoke($request);
//        echo 'Implements service dispatch rule';
//        echo "<br />";
//        echo 'Service requested: '.print_r($serviceName, true);
//        $this->securityManager->beforeInvoke($request);
    }

    public function get($serviceName) {
        $this->any($serviceName);
    }

    public function post($serviceName) {
        $this->any($serviceName);
    }

    public function put($serviceName) {
        $this->any($serviceName);
    }

    public function delete($serviceName) {
        $this->any($serviceName);
    }

}