<?php
/**
 * Created by JetBrains PhpStorm.
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

namespace Zeedhi\Util\Messages;

/**
 * Class Message
 *
 * @package Zeedhi\Util\Messages
 */
class Message
{

    /** @var $text string Message's text. */
    private $text;
    /** @var $parameters array List of parameters for the message. */
    private $parameters;
    /** @var $messageType string*/
    private $messageType;

    /**
     * Alert type.
     */
    const ALERT      = "A";
    /**
     * Validation type.
     */
    const VALIDATION = "V";
    /**
     * Error type.
     */
    const ERROR      = "E";

    /**
     * @param string $messageType
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;
    }

    /**
     * @return string
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

}