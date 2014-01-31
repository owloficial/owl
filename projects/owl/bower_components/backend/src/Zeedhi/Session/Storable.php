<?php
namespace Zeedhi\Session;

/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 12/07/13
 * Time: 16:32
 */
interface Storable
{

    /**
     * Gets a value from the Storable set, using the value of $name as key.
     *
     * @abstract
     * @param $name
     * @return mixed
     */
    public function get($name);

    /**
     * Sets a value to the Storable set, using the $name as key to store $value.
     *
     * @abstract
     * @param $name
     * @param $value
     * @return mixed
     */
    public function set($name, $value);

    /**
     * Destroys all content from the storable class.
     *
     * @abstract
     * @return mixed
     */
    public function destroy();

}
