<?php
namespace Mvc4us\Composer;

use Composer\Script\Event;

/**
 *
 * @author erdem
 *
 */
final class Installer
{

    public static function postUpdate(Event $event)
    {
        echo 'POST UPDATE FROM MVC4P' . PHP_EOL;
        echo ';)' . PHP_EOL;
        var_dump($event);
    }
}
