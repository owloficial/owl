<?php
namespace Zeedhi\HTTP\Response;

class JSON extends \Zeedhi\HTTP\Response{

    const CONTENT_TYPE = 'application/json';

    function __construct($content = null, $statusCode = null, $statusText = null)
    {
        //@todo should this automatically encode/decode the content?
        $content = is_array($content) ? json_encode($content) : $content;
        parent::__construct($content, self::CONTENT_TYPE, $statusCode, $statusText);
    }

    public function setContent($content)
    {
        $content = is_array($content) ? json_encode($content) : $content;
        parent::setContent($content);
    }


}