<?php
namespace Mvc4us\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

final class Logger extends AbstractLogger
{

    private static $logLevel = array(
        LogLevel::EMERGENCY => 1,
        self::ALERT => 2,
        self::CRITICAL => 3,
        self::ERROR => 4,
        self::WARNING => 5,
        self::NOTICE => 6,
        self::INFO => 7,
        self::DEBUG => 8
    );

    public function log($level, $message, $trace = null)
    {
        ;
    }
}
