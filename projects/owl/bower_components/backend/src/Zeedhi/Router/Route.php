<?php
namespace Zeedhi\Router;

/**
 * Description of Route
 *
 * @author tuliopinto
 */
class Route {

    private $httpRequest;
    private $serviceDescriptor;

    /**
     * 
     * @param string $uri
     * @param string $httpMethod
     */
    public function __construct(\Zeedhi\Request\HttpRequest $httpRequest) {
        $this->httpRequest = $httpRequest;
        $this->initRoute();
    }

    /**
     * 
     * @throws Exception\RouteNotFound
     */
    private function initRoute() {
        $jsonRoutes = file_get_contents('../routes.json');
        $routes = json_decode($jsonRoutes, true);
        $uri = $this->getHttpUri();
        if (isset($routes[$uri]) === false) {
            throw new Exception\RouteNotFound();
        }
        $this->serviceDescriptor = $routes[$uri];
    }

    /**
     * @param type $application
     * @param type $request
     * @param type $response
     * @return type
     */
    public function dispatch($application, $request, $response) {
        $serviceDescriptor = explode("::", $this->serviceDescriptor);
        $class = $serviceDescriptor[0];
        $method = $serviceDescriptor[1];
        $controller = new $class($application);
        $controller->$method($request, $response);
    }

    /**
     * 
     * @return \Zeedhi\Request\HttpRequest
     */
    public function getHttpRequest() {
        return $this->httpRequest;
    }

    /**
     * 
     * @return string
     */
    public function getEventType() {
        return $this->getHttpRequest()->getEventType();
    }

    /**
     * 
     * @return string
     */
    public function getHttpData() {
        return $this->getHttpRequest()->getData();
    }

    /**
     * 
     * @return string
     */
    public function getHttpUri() {
        return $this->getHttpRequest()->getUri();
    }

    /**
     * 
     * @return string
     */
    public function getHttpMethod() {
        return $this->getHttpRequest()->getMethod();
    }

}