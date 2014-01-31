<?php
/**
 * Abstraction class for validation messages.
 *
 * PHP Version 5.3.3
 *
 * @category PHP
 * @package  Controller
 * @author   Paulo Pereira <paulo.pereira@teknisa.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  SVN: $Id: coding-standard-tutorial.xml,v 1.9 2008-10-09 15:16:47 cweiske Exp $
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
namespace Zeedhi\Util\Exception;

/**
 * Class ValidationException
 *
 * @package Zeedhi\Util\Exception
 */
abstract class ValidationException extends \Exception
{

    private $params = array();

    /**
     * Sets an array of parameters to this class.
     * If this method is used when there's already some parameters set, all those old parameters will be overwritten by
     * those new ones set.
     *
     * @param array $params
     */
    public function setParams(array $params = null)
    {
        $this->params = $params;
    }

    /**
     * Returns all the parameters already set to this instance.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Sets one message to the list of messages. This method is incremental. In other words, if there's already some
     * parameters set in this object, all this parameters will continue there, and this new one will be set in the final
     * of the array of parameters.
     *
     * @param $param
     */
    public function addParam($param)
    {
        array_push($this->params, $param);
    }

    /**
     * Get the first parameter in the array of parameters.
     *
     * @return mixed
     */
    public function getParam()
    {
        return array_shift($this->params);
    }

    /**
     * This method was thought to return a simple string, corresponding to the name of one message set in Zeedhi
     * Framework.
     *
     * @return mixed
     */
    public abstract function getMessageName();

}