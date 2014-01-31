<?php
namespace Zeedhi\HTTP;

class Response {

    const PROTOCOL_VERSION = '1.1';
    const CONTENT_TYPE_TEXT_HTML = "text/html";

    //http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html
	const STATUS_CODE_CONTINUE = "100"; // Section 10.1.1: Continue
	const STATUS_CODE_SWITCH_PROTOCOLS = "101"; // Section 10.1.2: Switching Protocols
	const STATUS_CODE_OK = "200"; // Section 10.2.1: OK
	const STATUS_CODE_CREATED = "201"; // Section 10.2.2: Created
	const STATUS_CODE_ACCEPTED = "202"; // Section 10.2.3: Accepted
	const STATUS_CODE_NON_AUTHORITATIVE = "203"; // Section 10.2.4: Non-Authoritative Information
	const STATUS_CODE_NO_CONTENT = "204"; // Section 10.2.5: No Content
	const STATUS_CODE_RESET_CONTENT = "205"; // Section 10.2.6: Reset Content
	const STATUS_CODE_PARTIAL_CONTENT = "206"; // Section 10.2.7: Partial Content
	const STATUS_CODE_MULTIPLE_CHOICES = "300"; // Section 10.3.1: Multiple Choices
	const STATUS_CODE_MOVED_PERMANENTLY = "301"; // Section 10.3.2: Moved Permanently
	const STATUS_CODE_FOUND = "302"; // Section 10.3.3: Found
	const STATUS_CODE_SEE_OTHER = "303"; // Section 10.3.4: See Other
	const STATUS_CODE_NOT_MODIFIED = "304"; // Section 10.3.5: Not Modified
	const STATUS_CODE_USE_PROXY = "305"; // Section 10.3.6: Use Proxy
	const STATUS_CODE_TEMPORARY_REDIRECTED = "307"; // Section 10.3.8: Temporary Redirect
	const STATUS_CODE_BAD_REQUEST = "400"; // Section 10.4.1: Bad Request
	const STATUS_CODE_UNAUTHORIZED = "401"; // Section 10.4.2: Unauthorized
	const STATUS_CODE_PAYMENT_REQUIRED = "402"; // Section 10.4.3: Payment Required
	const STATUS_CODE_FORBIDDEN = "403"; // Section 10.4.4: Forbidden
	const STATUS_CODE_NOT_FOUND = "404"; // Section 10.4.5: Not Found
	const STATUS_CODE_METHOD_NOT_ALLOWED = "405"; // Section 10.4.6: Method Not Allowed
	const STATUS_CODE_NOT_ACCEPTABLE = "406"; // Section 10.4.7: Not Acceptable
	const STATUS_CODE_PROXY_AUTHENTICATION_REQUIRED = "407"; // Section 10.4.8: Proxy Authentication Required
	const STATUS_CODE_REQUEST_TIME_OUT = "408"; // Section 10.4.9: Request Time-out
	const STATUS_CODE_CONFLICT = "409"; // Section 10.4.10: Conflict
	const STATUS_CODE_GONE = "410"; // Section 10.4.11: Gone
	const STATUS_CODE_LENGTH_REQUIRED = "411"; // Section 10.4.12: Length Required
	const STATUS_CODE_PRECONDITION_FAILED = "412"; // Section 10.4.13: Precondition Failed
	const STATUS_CODE_ENTITY_TOO_LARGE = "413"; // Section 10.4.14: Request Entity Too Large
	const STATUS_CODE_URI_TO_LARGE = "414"; // Section 10.4.15: Request-URI Too Large
	const STATUS_CODE_UNSUPPORTED_MEDIA_TYPE = "415"; // Section 10.4.16: Unsupported Media Type
	const STATUS_CODE_RANGE_NOT_SATISFIABLE = "416"; // Section 10.4.17: Requested range not satisfiable
	const STATUS_CODE_EXPECTATION_FAILED = "417"; // Section 10.4.18: Expectation Failed
	const STATUS_CODE_INTERNAL_SERVER_ERROR = "500"; // Section 10.5.1: Internal Server Error
	const STATUS_CODE_NOT_IMPLEMENTED = "501"; // Section 10.5.2: Not Implemented
	const STATUS_CODE_BAD_GATEWAY = "502"; // Section 10.5.3: Bad Gateway
	const STATUS_CODE_UNAVAILABLE = "503"; // Section 10.5.4: Service Unavailable
	const STATUS_CODE_GATEWAY_TIME_OUT = "504"; // Section 10.5.5: Gateway Time-out
	const STATUS_CODE_HTTP_VERSION_NOT_SUPPORTED = "505"; // Section 10.5.6: HTTP Version not supported

    public static $statusTexts = array(
        self::STATUS_CODE_CONTINUE                  => 'Continue',
        self::STATUS_CODE_SWITCH_PROTOCOLS          => 'Switching Protocols',
        self::STATUS_CODE_OK                        => 'OK',
        self::STATUS_CODE_CREATED                   => 'Created',
        self::STATUS_CODE_ACCEPTED                  => 'Accepted',
        self::STATUS_CODE_NON_AUTHORITATIVE         => 'Non-Authoritative Information',
        self::STATUS_CODE_NO_CONTENT                => 'No Content',
        self::STATUS_CODE_RESET_CONTENT             => 'Reset Content',
        self::STATUS_CODE_PARTIAL_CONTENT           => 'Partial Content',
//        self::207 => 'Multi-Status',          // RFC4918
//        self::208 => 'Already Reported',      // RFC5842
//        self::226 => 'IM Used',               // RFC3229
        self::STATUS_CODE_MULTIPLE_CHOICES          => 'Multiple Choices',
        self::STATUS_CODE_MOVED_PERMANENTLY         => 'Moved Permanently',
        self::STATUS_CODE_FOUND                     => 'Found',
        self::STATUS_CODE_SEE_OTHER                 => 'See Other',
        self::STATUS_CODE_NOT_MODIFIED              => 'Not Modified',
        self::STATUS_CODE_USE_PROXY                 => 'Use Proxy',
//        self::306 => 'Reserved',
//        self::307 => 'Temporary Redirect',
//        self::self::308 => 'Permanent Redirect',    // RFC-reschke-http-status-308-07
        self::STATUS_CODE_BAD_REQUEST                   => 'Bad Request',
        self::STATUS_CODE_UNAUTHORIZED                  => 'Unauthorized',
        self::STATUS_CODE_PAYMENT_REQUIRED              => 'Payment Required',
        self::STATUS_CODE_FORBIDDEN                     => 'Forbidden',
        self::STATUS_CODE_NOT_FOUND                     => 'Not Found',
        self::STATUS_CODE_METHOD_NOT_ALLOWED            => 'Method Not Allowed',
        self::STATUS_CODE_NOT_ACCEPTABLE                => 'Not Acceptable',
        self::STATUS_CODE_PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
        self::STATUS_CODE_REQUEST_TIME_OUT              => 'Request Timeout',
        self::STATUS_CODE_CONFLICT                      => 'Conflict',
        self::STATUS_CODE_GONE                          => 'Gone',
        self::STATUS_CODE_LENGTH_REQUIRED               => 'Length Required',
        self::STATUS_CODE_PRECONDITION_FAILED           => 'Precondition Failed',
        self::STATUS_CODE_ENTITY_TOO_LARGE              => 'Request Entity Too Large',
        self::STATUS_CODE_URI_TO_LARGE                  => 'Request-URI Too Long',
        self::STATUS_CODE_UNSUPPORTED_MEDIA_TYPE        => 'Unsupported Media Type',
        self::STATUS_CODE_RANGE_NOT_SATISFIABLE         => 'Requested Range Not Satisfiable',
        self::STATUS_CODE_EXPECTATION_FAILED            => 'Expectation Failed',
//        self::STATUS_CODE_u=> 'Unprocessable Entity',                                        // RFC4918
//        self::423 => 'Locked',                                                      // RFC4918
//        self::424 => 'Failed Dependency',                                           // RFC4918
//        self::self::425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
//        self::426 => 'Upgrade Required',                                            // RFC2817
//        self::428 => 'Precondition Required',                                       // RFC6585
//        self::429 => 'Too Many Requests',                                           // RFC6585
//        self::431 => 'Request Header Fields Too Large',                             // RFC6585
        self::STATUS_CODE_INTERNAL_SERVER_ERROR         => 'Internal Server Error',
        self::STATUS_CODE_NOT_IMPLEMENTED               => 'Not Implemented',
        self::STATUS_CODE_BAD_GATEWAY                   => 'Bad Gateway',
        self::STATUS_CODE_UNAVAILABLE                   => 'Service Unavailable',
        self::STATUS_CODE_GATEWAY_TIME_OUT              => 'Gateway Timeout',
        self::STATUS_CODE_HTTP_VERSION_NOT_SUPPORTED    => 'HTTP Version Not Supported',
//        self::STATUS_CODE_ => 'Variant Also Negotiates (Experimental)',                      // RFC2295
//        self::        507 => 'Insufficient Storage',                                        // RFC4918
//        self::508 => 'Loop Detected',                                               // RFC5842
//        self::510 => 'Not Extended',                                                // RFC2774
//        self::STATUS_CODE_ 511 => 'Network Authentication Required',                             // RFC6585
    );

    protected $content;
    protected $contentType;
    protected $version;
    protected $statusCode;

    function __construct($content = null, $contentType = null, $statusCode = null)
    {
        $this->content = $content;
        //@todo review default content type
        $this->contentType = $contentType ?: self::CONTENT_TYPE_TEXT_HTML;
        $this->statusCode = $statusCode ?: self::STATUS_CODE_OK;
        $this->version = self::PROTOCOL_VERSION;
    }

    protected function sendHeaders() {
        // headers have already been sent by the developer
        if (headers_sent()) {
            return $this;
        }

        $statusText = self::$statusTexts[$this->statusCode];
        header("HTTP/{$this->version} {$this->statusCode} {$statusText}");
        header("Content-Type: {$this->contentType}");
        //@todo use Header class to parameter other properties like "last modified", "expires" etc.

        return $this;
    }

    protected function sendContent() {
        echo $this->content;
        return $this;
    }

    public function send() {
        $this->sendHeaders()->sendContent();
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param string $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }
}