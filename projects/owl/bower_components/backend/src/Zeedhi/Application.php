<?php
namespace Zeedhi;

use Zeedhi\ErrorHandler\ErrorHandlerService;
/**
 * Description of Bootstrap
 *
 * @author tuliopinto
 */
class Application {

    /** @var Application */
    protected static $instance;
    /** @var \Zeedhi\Router\Provider */
    protected $router;
    /** @var array */
    protected $configData;
    /** @var  \Zeedhi\ErrorHandler\ErrorHandlerService */
    protected $errorHandler;
    /** @var  \Doctrine\ORM\EntityManager */
    protected $entityManager;
    /** @var  \Zeedhi\Data\DataSourceManager */
    protected $dataSourceManager;
    /** @var  \Zeedhi\Controller\SyncEngine */
    protected $syncEngine;

    /**
     * Application constructor.
     */
    private function __construct($serviceUrl) {
        $serviceUrl = ($serviceUrl == null) ? $_SERVER["SCRIPT_FILENAME"] : $serviceUrl;
        $dirName = dirname($serviceUrl);
        $json = file_get_contents($dirName .'/../app.json');
        $this->configData = json_decode($json, true);
    }

    /**
     * @return Application
     */
    public static function getInstance($serviceUrl = null) {
        if(self::$instance === null){
            self::$instance = new self($serviceUrl);
        }

        return self::$instance;
    }

    public function initErrorHandler() {
        $errorHandler = new ErrorHandlerService($this);
        $errorHandler->register();
        return $errorHandler;
    }

    public function initDataSourceManager() {
        $dataSourceManager = new $this->configData['database']['dataSourceManager']($this->getEntityManager());
        $this->dataSourceManager = $dataSourceManager;
    }

    public function initEntityManager() {
        $entityManagerConfig = $this->configData['database']['entityManager'];
        $connectionParams = $this->configData['database']['connection'];
        $isDevMode = (bool)$entityManagerConfig['is_dev_mode'];
        $path = array($entityManagerConfig['path']);
        switch ($entityManagerConfig['metadata_driver']) {
            case 'xml':
                $emConfig = \Doctrine\ORM\Tools\Setup::createXMLMetadataConfiguration($path, $isDevMode);
                break;
            case 'yaml':
                $emConfig = \Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration($path, $isDevMode);
                break;
            case 'annotation':
                $emConfig = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($path, $isDevMode);
                break;
            case 'php':
            default:
            throw new \Exception("Unsupported driver {$entityManagerConfig['metadata_driver']}.");
                break;
        }

        $eventManager = new \Doctrine\Common\EventManager();
        $eventManager->addEventSubscriber(new \Doctrine\DBAL\Event\Listeners\OracleSessionInit());
        $this->entityManager = \Doctrine\ORM\EntityManager::create($connectionParams, $emConfig, $eventManager);
    }

    /**
     * Initialize router.
     */
    public function initRouter() {
        $routerConfig = new Config\RouterConfig($this->configData['router']);
        $this->router = new \Zeedhi\Router\Provider($this, $routerConfig);
    }

    /**
     * @return \Zeedhi\Router\Provider
     */
    public function getRouter() {
        if($this->router === null) {
            $this->initRouter();
        }
        return $this->router;
    }

    /**
     * Initialize syncEngine.
     */
    public function initSyncEngine() 
    {
        if (isset($this->configData["router"]["syncEngine"])) {
            if (class_exists($this->configData["router"]["syncEngine"])) {
                $this->syncEngine = new $this->configData["router"]["syncEngine"]();
            } else {
                throw new \Exception("Unsupported sync Engine ". $this->configData["router"]["syncEngine"]);
            }
        }

        return $this->syncEngine;
    }

    /**
     * Return a sync engine instance.
     * @return \Zeedhi\Controller\SyncEngine
     */
    public function getSyncEngine() 
    {
        return $this->syncEngine;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(){
        if($this->entityManager === null) {
            $this->initEntityManager();
        }

        return $this->entityManager;            
    }

    /**
     * @return Data\DataSourceManager
     */
    public function getDataSourceManager(){
        if($this->dataSourceManager === null) {
            $this->initDataSourceManager();
        }

        return $this->dataSourceManager;            
    }    

    /**
     * Execute route and dispatch.
     * @return void
     */
    public function run() {
        $this->getRouter()->run();
    }

    public function init() {
        $this->getRouter()->init();
        $this->initErrorHandler()
             ->setRequest($this->getRouter()->getRequest())
             ->setResponse($this->getRouter()->getResponse());
        $this->initSyncEngine();
        return $this;
    }

    /**
     * @param string $dataSourceName
     * @return array
     */
    public function getDataSourceConfig($dataSourceName) {        
        $json = file_get_contents(SERVICE_PATH.'\\..\\gen\\datasources\\'.$dataSourceName.'.json');
        $dataConfig = json_decode($json, true);
        return $dataConfig[$dataSourceName];
    }

    public function getSyncConfig()
    {
        return $this->configData["sync_engine"];
    }

    /**
     * Base URI.
     *
     * @return mixed
     */
    public function getBaseUri()
    {
        return $this->configData["router"]["baseUri"];
    }

    public function getMongoConfig()
    {
        return $this->configData["mongodb"]["connection"];
    }

}