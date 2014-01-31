<?php
/**
 * Handle different types of messages in a Zeedhi Project.
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

use Zeedhi\Util\Exception\ValidationException;

/**
 * Class MessageHandler.
 *
 * This class provides to basic methods, used to handle any message in a Zeedhi project.
 * This class classifies all messages sent to this in one of these three kinds: Validation messages, error messages and
 * a list of messages to be shown in group. These three types will be explained better bellow.
 *
 * @package Zeedhi\Util\Messages
 */
class MessageHandler
{

    private static $abort = false;

    /**
     * This flag is used to know if this class must show error messages (error messages like ORA errors).
     *
     * @var bool
     */
    private static $SHOW_ERROR_MESSAGES = true;

    /**
     * Shows the messages sent to this class. This method classifies the message sent in three groups:
     * * The first one, the validation messages, expects an instance of \Zeedhi\Util\Exception\ValidationException
     *   class. This kind of messages were thought to be used in validations in a Zeedhi project, where the message
     *   to be shown was set to Zeedhi framework.
     * * The second one, the list of messages. If this method was called to show all method accumulated (using the
     *   MessageList::ID_MESSAGE_LIST constant as exception message), this method will exhibit all messages accumulated
     *   until now.
     * * The last group, the error messages, shows any kind of exceptions sent to this class. This kind of messages will
     *   be exhibited as error messages.
     *
     *
     * @param \Exception $e
     * @param bool $abort
     *
     * @return void
     */
    public static function showMessage(\Exception $e, $abort = false)
    {
        self::$abort = $abort;

        if ($e instanceof ValidationException) {
            self::handleValidationMessages($e);
        } else if ($e->getMessage() === MessageList::ID_MESSAGE_LIST) {
            self::handleMessageList();
        } else {
            self::handleErrors($e);
        }
    }

    /**
     * Shows a list of errors. This method can show messages as simple messages or like abort methods (this second kind
     * of messages interrupts all execution if used).
     *
     * @param bool $abort Flag used to verify if it's necessary to abort the application.
     *
     * @return void
     */
    public static function showAccumulatedMessages($abort = false)
    {
        if ($abort) {
            self::abortMessages();
        } else {
            self::handleMessageList();
        }
    }

    /**
     * This method is used when ValidationException's must be exhibited. It will show any message handled by Zeedhi
     * Framework.
     *
     * @param ValidationException $e ValidationException instance.
     *
     * @return void
     */
    private static function handleValidationMessages(ValidationException $e)
    {
        try {
            $zeedhiMessage = $e->getMessageName();
            $params = $e->getParams();
            $message = self::getZeedhiMessageByName($zeedhiMessage, $params);
            self::exhibitMessage($message, Message::ALERT);
        } catch (\Exception $e) {
            self::showMessage($e, self::$abort);
        }
    }

    /**
     * Handle error messages.
     *
     * @param \Exception $e Exception instance.
     *
     * @return void
     */
    private static function handleErrors(\Exception $e)
    {
        if (self::$SHOW_ERROR_MESSAGES) {
            self::exhibitMessage($e->getMessage(), Message::ERROR);
        }
    }

    /**
     * Handle the print of a list of messages.
     *
     * @return void
     */
    private static function handleMessageList()
    {
        while(MessageList::getInstance()->hasMessages()) {
            $messageObj = MessageList::getInstance()->getMessage();
            $message    = self::getZeedhiMessageByName($messageObj->getText(), $messageObj->getParameters());
            self::exhibitMessage($message, $messageObj->getMessageType());
        }
    }

    /**
     * Handle abortion messages. This method will show the messages and then, stop the application.
     *
     * @return void
     */
    private static function abortMessages()
    {
        $messages = "";
        while(MessageList::getInstance()->hasMessages()) {
            $message  = MessageList::getInstance()->getMessage();
            $params   = is_array($message->getParameters()) ? $message->getParameters() : array();
            $messages .= "- " . self::getZeedhiMessageByName($message->getText(), $params)."<br>";
        }

        if ((bool)$messages) {
            self::exhibitMessage($messages, Message::ERROR, true);
        }
    }

    /**
     * This method is used to print any kind of message.
     *
     * @todo change the method "var_dump" used here to the method to exhibit messages in a zeedhi project (similar to
     * tk_warning, tk_message, tk_abort, etc).
     *
     * @param $message
     * @param $messageType
     * @param bool $abort
     */
    private static function exhibitMessage($message, $messageType, $abort = false)
    {
        if (self::$abort || $abort) {
            var_dump(array("message" => $message, "messageType" => "abort"));
        } else if ($messageType === Message::ERROR) {
            var_dump(array("message" => $message, "messageType" => "error"));
        } else if ($messageType === Message::ALERT) {
            var_dump(array("message" => $message, "messageType" => "warning"));
        } else {
            var_dump(array("message" => $message, "messageType" => "validation"));
        }
    }

    /**
     * Get the messages from Zeedhi Framework.
     *
     * @todo change this method to use the the method in Zeedhi framework, similar to tk_get_message_by_name
     * @param $zeedhiMessage
     * @param $params
     *
     * @return string
     */
    private static function getZeedhiMessageByName($zeedhiMessage, $params)
    {
        return strtoupper($zeedhiMessage);
    }

}