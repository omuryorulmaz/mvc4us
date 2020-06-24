<?php
namespace Mvc4us\Config\Exception;

/**
 *
 * @author erdem
 *        
 */
class InvalidConfigException extends \RuntimeException
{

    public function __construct($message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

