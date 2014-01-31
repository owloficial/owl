<?php
    namespace Zeedhi\Request;
    /**
     * Description of Request
     *
     * @author tuliopinto
     */
    abstract class Request {

        private $url;
        private $params;
        private $requestType;

        const CONTAINER_NAME_PARAM_KEY = "ContainerName";
        const WIDGET_NAME_PARAM_KEY = "WidgetName";


        public function setUrl($url)
        {
            $this->url = $url;
        }

        public function getUrl()
        {
            return $this->url;
        }

        public function setParams($params)
        {
            $this->params = $params;
        }

        public function getParams()
        {
            return $this->params;
        }

        public function setRequestType($requestType)
        {
            $this->requestType = $requestType;
        }

        public function getRequestType()
        {
            return $this->requestType;
        }

        public function getAllHeaders()
        {
            $headers = '';
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }

            return $headers;
        }

}