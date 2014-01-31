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

use \Zeedhi\Util\Messages\Message;

/**
 * This class is used to accumulate and later exhibit that messages.
 *
 * Some process can have various steps accumulating some messages and then, just in the end of a macro process, this
 * messages must be shown. So, its necessary to have a way to capture and store all this messages to exhibit them in the
 * end.
 *
 * This class provides some services to accumulate messages, identify which type of messages were stored and retrieve
 * that messages (this class was thought to work like a queue of messages).
 *
 * @package Zeedhi\Util\Messages
 */
class MessageList
{
    /**
     * Message accumulator identifier. This constant must be used in classes like MessageHandler, when it's necessary to
     * know if the object to be handled is an instance of Message or if it is a list of messages.
     */
    const ID_MESSAGE_LIST = "ae371e76160f02fa7563df6e0819fe5bff5c6b8d";

    /**
     * This object stores all messages set to this object.
     *
     * @var array
     */
    private $list;
    /**
     * @var int
     */
    private $counter;
    /**
     * @var
     */
    private $errorCounter;

    private static $instance;

    /**
     * Constructor method.
     */
    private function __construct()
    {
        $this->list    = array();
        $this->counter = 0;
    }

    /**
     * Singleton instance provider. This method allows that just one instance stores all messages in a macro process.
     *
     * @return MessageList Returns the instance of this class.
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Adds a new message in the message list.
     *
     * @param string $text        The body of this message.
     * @param array  $parameters  Possible parameters used in this message. By default, this is used as null.
     * @param string $messageType Describes of which type this message is.
     *
     * @return void
     */
    public function addMessage($text, $parameters = null, $messageType = Message::ALERT)
    {
        $message = new Message();
        $message->setText($text);
        $message->setParameters($parameters);
        $message->setMessageType($messageType);

        $this->list[$this->counter] = $message;
        $this->counter++;
        if ($messageType === Message::ERROR) {
            $this->errorCounter++;
        }
    }

    /**
     * Returns the next message, using the strategy of queue (the first message set to the list is the first message
     * returned by this method).
     *
     * @return Message
     */
    public function getMessage()
    {
        $message = array_shift($this->list);
        $this->counter--;
        if ($message->getMessageType() === Message::ERROR) {
            $this->errorCounter--;
        }

        return $message;
    }

    /**
     * Verifies if there are error messages stored in this list.
     *
     * @return bool
     */
    public function hasErrorMessages()
    {
        return $this->errorCounter > 0;
    }

    /**
     * Verifies if there are messages of any type in the list.
     *
     * @return bool
     */
    public function hasMessages()
    {
        return $this->counter > 0;
    }

}