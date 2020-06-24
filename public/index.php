<?php
use Mvc4us\Mvc4us;

/**
 * Absolute application path
 *
 * @var string
 */
define('APP_DIR', dirname(__DIR__));

require APP_DIR . '/vendor/autoload.php';

$mvc4p = new Mvc4us(APP_DIR);
$mvc4p->runWeb();
