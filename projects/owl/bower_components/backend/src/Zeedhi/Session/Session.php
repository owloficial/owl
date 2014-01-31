<?php
/**
 * Created by JetBrains PhpStorm.
 * User: lucassouza
 * Date: 16/11/11
 * Time: 17:42
 * To change this template use File | Settings | File Templates.
 */
namespace Zeedhi\Session;

class Session implements \Zeedhi\Session\Storable
{

    public function __construct()
    {
        session_start();
        session_regenerate_id();

        /**
         * PHP $_SESSION security treatment.
         * @see http://phpsec.org/projects/guide/4.html
         */
        if (isset($_SESSION['session_token_id'])) {
            if ($_SESSION['session_token_id'] != md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])) {
                exit;
            }
        } else {
            $_SESSION['session_token_id'] = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
        }
    }

    /**
     * Gets a value from the Storable set, using the value of $name as key.
     *
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        // add "isset" to evade "Unknown error type: [8] Undefined index {$name}".
        if(isset($_SESSION[$name])) return $_SESSION[$name];
        return null;
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
        $_SESSION[$name] = $value;
    }

    /**
     * Destroys all content from the storable class.
     *
     * @return mixed
     */
    public function destroy() {
        session_destroy();
    }
    
}