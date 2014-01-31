<?php
namespace Zeedhi\Request;

/**
 * Description of FrontRequest
 *
 * @author tuliopinto
 */
class HttpRequest {

    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    private $method;
    private $baseUri;
    private $uri;
    private $data = array();

    /**
     * HttpRequest constructor
     */
    public function __construct($baseUri = null) {
        $this->baseUri = $baseUri;

        // Define HTTP METHOD
        $this->method = isset($_SERVER['REQUEST_METHOD'])
            ? strtoupper($_SERVER['REQUEST_METHOD'])
            : self::METHOD_GET;

        // Define HTTP URI
        $this->uri = isset($_SERVER['REQUEST_URI']) 
            ? parse_url($_SERVER['REQUEST_URI'] , PHP_URL_PATH)
            : parse_url('/', PHP_URL_PATH);
            /**
             * CORS Support, devepment only
             * Refactor
             */

            // Allow from any origin
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: 86400');    // cache for 1 day
            }

            // Access-Control headers are received during OPTIONS requests
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

                exit(0);
            }

        // Cleanup URI
        $this->cleanup();
        $this->discoverData();
    }

    /**
     * 
     * @return type
     */
    public function discoverEventType() {
        if ( ($this->data && isset($this->data['requestType'])) === false) {
            throw new Exception\RequestTypeNotFound();
        }
    }

    /**
     * Discover data sent.
     */
    public function discoverData() {
        if($this->isPost()) {
            $this->data = $_REQUEST;
        } elseif($this->isGet()) {
            $this->data = $_GET;
        } else {
            parse_str(file_get_contents("php://input"), $this->data);
        }

        $this->discoverEventType();
    }

    /**
     * Cleanup URI removing last bar and baseUri.
     */
    private function cleanup() {
        $this->uri = rtrim($this->uri, ' /'); // cleanup last bar
    
        if($this->baseUri) {
            $this->uri = preg_replace('#^('.$this->baseUri.')#', '', $this->uri);
        }
    }

    /**
     * 
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * 
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * 
     * @return bool
     */
    public function isPost() {
        return self::METHOD_POST == $this->method;
    }

    /**
     * 
     * @return bool
     */
    public function isGet() {
        return self::METHOD_GET == $this->method;
    }

    /**
     * 
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * 
     * @return string
     */
    public function getEventType() {
        return $this->data['requestType'];
    }

}
