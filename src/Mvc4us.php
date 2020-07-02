<?php
namespace Mvc4us;

use Mvc4us\Config\Config;
use Mvc4us\Http\Request;
use Mvc4us\Http\Response;
use Mvc4us\Loader\RouteLoader;
use Mvc4us\Loader\ServiceLoader;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

/**
 *
 * @author erdem
 *
 */
class Mvc4us
{

    const RUN_CMD = 1;

    const RUN_WEB = 2;

    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     *
     * @var string
     */
    private $projectDir;

    public function __construct($projectDir, $environment = null)
    {
        $this->projectDir = $projectDir;
        $this->reload($environment);
    }

    public function reload($environment = null)
    {
        Config::load($this->projectDir, $environment);

        $this->container = ServiceLoader::load($this->projectDir);

        $router = RouteLoader::load($this->projectDir);
        $this->container->set('router', $router);
    }

    public function runCmd($controllerName, ?Request $request = null, $echo = true): ?Response
    {
        $request = $request ?? new Request($_SERVER['argv']);
        $request->setMethod('CLI');
        $response = $this->run($controllerName, $request, self::RUN_CMD);
        if ($echo) {
            echo $response->getContent() . PHP_EOL;
        }
        return $response;
    }

    public function runWeb(?Request $request = null): ?Response
    {
        $request = $request ?? Request::createFromGlobals();
        $response = $this->run(null, $request, self::RUN_WEB);
        if (PHP_SAPI === 'cli') {
            return $response;
        }
        $response->send();
        return null;
    }

    private function run($controllerName, ?Request $request, $run): ?Response
    {
        $e = null;
        try {
            if ($this->container === null) {
                $this->reload();
            }

            $response = new Response();
            $response->prepare($request);

            if ($run === self::RUN_WEB && $controllerName === null) {

                /**
                 *
                 * @var \Symfony\Component\Routing\Router $router
                 */
                $router = $this->container->get('router');

                $context = new RequestContext();
                $context->fromRequest($request);
                $router->setContext($context);
                $matcher = $router->getMatcher();

                if ($matcher instanceof RequestMatcherInterface) {
                    $parameters = $matcher->matchRequest($request);
                } else {
                    $parameters = $matcher->match($request->getPathInfo());
                }

                $request->attributes->add($parameters);

                $controllerName = $request->attributes->get('_controller');
            }
            if ($controllerName == null) {
                throw new ResourceNotFoundException('Controller is not specified.');
            }

            /**
             *
             * @var \Mvc4us\Controller\ControllerInterface $controller
             */
            $controller = $this->container->get($controllerName);
            $controller->handle($request, $response);
        } catch (ResourceNotFoundException $e) {

            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            if ($run === self::RUN_WEB) {
                $message = sprintf('No routes found for "%s %s"', $request->getMethod(), $request->getPathInfo());

                if ($referer = $request->headers->get('referer')) {
                    $message .= sprintf(' (Referer: "%s")', $referer);
                }

                $reflectionObject = new \ReflectionObject($e);
                $reflectionObjectProp = $reflectionObject->getProperty('message');
                $reflectionObjectProp->setAccessible(true);
                $reflectionObjectProp->setValue($e, $message);
            }
        } catch (MethodNotAllowedException $e) {

            $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
            $message = sprintf(
                'No routes found for "%s %s". Method Not Allowed (Allow: %s)',
                $request->getMethod(),
                $request->getPathInfo(),
                implode(', ', $e->getAllowedMethods()));

            $reflectionObject = new \ReflectionObject($e);
            $reflectionObjectProp = $reflectionObject->getProperty('message');
            $reflectionObjectProp->setAccessible(true);
            $reflectionObjectProp->setValue($e, $message);
        } catch (ServiceNotFoundException $e) {

            $response->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (InvalidArgumentException $e) {

            $response->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (ServiceCircularReferenceException $e) {

            $response->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (\Exception $e) {

            $response->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if ($e !== null) {
            $response->setException($e);
            // TODO logger service
            error_log($e);
        }
        return $response;
    }
}
