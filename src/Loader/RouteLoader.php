<?php
namespace Mvc4us\Loader;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Loader\PhpFileLoader;

/**
 *
 * @author erdem
 */
final class RouteLoader
{

    /**
     * This class should not be instantiated.
     */
    private function __construct()
    {}

    public static function load($projectDir): Router
    {
        $routeLocator = new FileLocator($projectDir . '/config/routes');
        $routeLoader = new PhpFileLoader($routeLocator);

        $router = new Router($routeLoader, 'routes.php');
        return $router;
    }
}

