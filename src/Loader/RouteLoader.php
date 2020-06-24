<?php
namespace Mvc4us\Loader;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Routing\RouteCollection;
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

    public static function load($projectDir): RouteCollection
    {
        $routeLocator = new FileLocator($projectDir . '/config/routes');
        $routeLoader = new PhpFileLoader($routeLocator);
        try {
            $routes = $routeLoader->load('routes.php');
        } catch (FileLocatorFileNotFoundException $e) {
            $routes = new RouteCollection();
        }
        return $routes;
    }
}

