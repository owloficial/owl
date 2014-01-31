<?php
namespace Zeedhi\Session;

/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 12/07/13
 * Time: 16:23
 */
class Memcache implements \Zeedhi\Session\Storable
{

    // Root of all memcache data.
    const MEMCACHE_ROOT = "331fb3081ad9418ec26ed160da3cde67bed2ea8f";

    /**
     * @var \Memcache
     */
    private $memcache;

    /**
     * @var \Zeedhi\Session\Memcache
     */
    private static $instance = null;


    private function __construct()
    {
        $obj = array();
        $this->connect();

        $this->memcache->set(self::MEMCACHE_ROOT, $obj);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Creates a connection with Memcache server.
     */
    private function connect()
    {
        $json = file_get_contents(__DIR__."/config/config.json");
        $json = json_decode($json, true);

        $this->memcache = new \Memcache();
        $this->memcache->connect($json["connection"]["host"], $json["connection"]["port"]);
    }

    /**
     * Gets a value from the Storable set, using the value of $name as key.
     *
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        $obj = $this->memcache->get(self::MEMCACHE_ROOT);
        return isset($obj[$name]) ? $obj[$name] : null;
    }

    /**
     * Sets a value to the Storable set, using the $name as key to store $value.
     *
     * @param $name
     * @param $value
     * @return mixed
     */
    public function set($name, $value)
    {
        $obj = $this->memcache->get(self::MEMCACHE_ROOT);
        $obj[$name] = $value;
        $this->memcache->set(self::MEMCACHE_ROOT, $obj);
    }

    /**
     * Destroys all content from the storable class.
     *
     * @return mixed
     */
    public function destroy()
    {
        $this->memcache->delete(self::MEMCACHE_ROOT);
    }
}
