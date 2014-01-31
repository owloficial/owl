<?php
namespace Zeedhi\Service;

class Router implements \Respect\Rest\Routable
{
    private $routes;

    public function __construct($routes){
        $this->routes = $routes;
    }
    public function any($serviceName) {
        try {
            $rest = $this->routes[join('/', $serviceName)];
            $class_data = explode("::", $rest);

            if (!class_exists($class_data[0])) {
                throw new Exception\RouteNotFoundException("This route does not exist.");
            }

            $restObj = new $class_data[0]();
            $restObj->$class_data[1]($teste);
        } catch (\Exception $e) {
            throw $e;
        }
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
