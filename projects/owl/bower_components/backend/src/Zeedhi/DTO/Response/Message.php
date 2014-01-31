<?php
namespace Zeedhi\DTO\Response;

class Message {
    const TYPE_MESSAGE  = 'M';
    const TYPE_WARNING  = 'W';
    const TYPE_ERROR    = 'E';

    /** @var string */
    protected $type;
    /** @var string */
    protected $message;
    /** @var integer */
    protected $fadeTime;

    function __construct($message, $type = null, $fadeTime = null)
    {
        $this->message = $message;
        $this->type = $type ?: self::TYPE_MESSAGE;
        $this->fadeTime = $fadeTime ?: self::fadeTimeByType($this->type);
    }

    /**
     * @param $type
     * @return int Fade time value in microseconds.
     */
    public static function fadeTimeByType($type) {
        switch($type) {
            case self::TYPE_ERROR:
                $fadetime = 30000;
                break;
            case self::TYPE_WARNING:
            case self::TYPE_MESSAGE:
            default:
                $fadetime = 5000;
                break;
        }

        return $fadetime;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getFadeTime()
    {
        return $this->fadeTime;
    }
}