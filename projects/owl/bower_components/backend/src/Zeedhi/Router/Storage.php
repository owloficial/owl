<?php
namespace Zeedhi\Router;

/**
 * User: paulopereira
 * Date: 12/07/13
 * Time: 17:35
 * To change this template use File | Settings | File Templates.
 */
class Storage
{
    const ROUTE_STORAGE_ROOT = "routes";

    /**
     * Attribute used to keep a different root in the store session for each of the projects using the same session
     * server.
     *
     * @var string
     */
    private $routerScope;
    /**
     * Storable instance, used to access the session where the list of routes will be kept.
     *
     * @var \Zeedhi\Session\Storable
     */
    private $storage;

    public function __construct(\Zeedhi\Session\Storable $storage)
    {
        $this->routerScope = sha1($_SERVER["SCRIPT_FILENAME"]);
        $this->storage     = $storage;
    }

    /**
     * Sets a list of routes in the session storage. Since this list is shared by all the users, it will only be set
     * if there is nothing yet.
     *
     * @param array $list
     */
    public function setRouteList($list = array())
    {
        if (!$this->storage->get($this->routerScope)) {
            $this->storage->set($this->routerScope, array(self::ROUTE_STORAGE_ROOT => $list));
        }
    }

    /**
     * This method will return the route represented by the alias passed in parameter. If this alias doesn't exists in
     * session, null will be returned.
     *
     * @param $routeAlias
     * @return null|String
     */
    public function getRoute($routeAlias)
    {
        $routeList  = $this->storage->get($this->routerScope);
        $routeList  = $routeList[self::ROUTE_STORAGE_ROOT];

        $route      = null;
        if (isset($routeList[$routeAlias])) {
            $route = $routeList[$routeAlias];
        }

        return $route;
    }

}
