<?php
namespace Zeedhi\Request\Exception;

/**
 * Description of EventTypeNotFound
 *
 * @author tuliopinto
 */
class RequestTypeNotFound extends \RuntimeException{
    
    protected $message = 'The requestType attribute was not found in request.';
    
}
