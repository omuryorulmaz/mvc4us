<?php
namespace Mvc4us\Controller;

use Mvc4us\Controller\Exception\CircularForwardException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 *
 * @author erdem
 *
 */
abstract class AbstractController implements ControllerInterface
{

    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    private static $callStack = [];

    public function setContainer(ContainerInterface $container): ?ContainerInterface
    {
        $previous = $this->container;
        $this->container = $container;

        return $previous;
    }

    /**
     * Gets a container parameter by its name.
     *
     * @deprecated
     *
     * @return mixed
     */
    protected function getParameter(string $name)
    {
        // if (! $this->container->has('parameter_bag')) {
        throw new ServiceNotFoundException(
            'parameter_bag',
            null,
            null,
            [],
            sprintf(
                'The "%s::getParameter()" method is missing a parameter bag to work properly. Did you forget to register your controller as a service subscriber? This can be fixed either by using autoconfiguration or by manually wiring a "parameter_bag" in the service locator passed to the controller.',
                \get_class($this)));
        // }

        // return $this->container->get('parameter_bag')->get($name);
    }

    /**
     * Returns true if the service id is defined.
     */
    protected function has(string $id): bool
    {
        return $this->container->has($id);
    }

    /**
     * Gets a container service by its id.
     *
     * @return object The service
     */
    protected function get(string $id)
    {
        return $this->container->get($id);
    }

    /**
     * Generates a URL from the given parameters.
     * TODO
     *
     * @see \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    protected function generateUrl(string $route, array $parameters = array(), int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * Forwards the request to another controller.
     *
     * @param string $controllerName
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Mvc4us\Controller\Exception\CircularForwardException
     */
    protected function forward(string $controllerName, Request $request): Response
    {
        self::$callStack[static::class] = true;
        if (array_key_exists($controllerName, self::$callStack)) {
            throw new CircularForwardException('Infinite forward loop.');
        }

        /**
         *
         * @var \Mvc4us\Controller\AbstractController $controller
         */
        $controller = $this->container->get($controllerName);
        $controller->setContainer($this->container);
        $response = $controller->handle($request);
        unset(self::$callStack[static::class]);
        return $response;
    }

    /**
     * Returns true if current call is a forwarded call.
     *
     * @return boolean
     */
    protected function isForwarded()
    {
        return ! empty(self::$callStack);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url
     * @param int $status
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirect(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param string $route
     * @param array $parameters
     * @param int $status
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectToRoute(string $route, array $parameters = array(), int $status = 302): RedirectResponse
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }

    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     * TODO
     */
    protected function json($data, int $status = 200, array $headers = array(), array $context = array()): JsonResponse
    {
        if ($this->container->has('serializer')) {
            $json = $this->container->get('serializer')->serialize(
                $data,
                'json',
                array_merge(array(
                    'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS
                ), $context));

            return new JsonResponse($json, $status, $headers, true);
        }

        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Returns a BinaryFileResponse object with original or customized file name and disposition header.
     * TODO
     *
     * @param \SplFileInfo|string $file
     *            File object or path to file to be sent as response
     */
    protected function file($file, string $fileName = null, string $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT): BinaryFileResponse
    {
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(
            $disposition,
            null === $fileName ? $response->getFile()->getFilename() : $fileName);

        return $response;
    }

    /**
     * Adds a flash message to the current session for type.
     * TODO
     *
     * @throws \LogicException
     */
    protected function addFlash(string $type, string $message)
    {
        if (! $this->container->has('session')) {
            throw new \LogicException(
                'You can not use the addFlash method if sessions are disabled. Enable them in "config/packages/framework.yaml".');
        }

        $this->container->get('session')->getFlashBag()->add($type, $message);
    }

    /**
     * Returns a rendered view.
     * TODO
     */
    protected function renderView(string $view, array $parameters = array()): string
    {
        if ($this->container->has('templating')) {
            return $this->container->get('templating')->render($view, $parameters);
        }

        if (! $this->container->has('twig')) {
            throw new \LogicException(
                'You can not use the "renderView" method if the Templating Component or the Twig Bundle are not available. Try running "composer require symfony/twig-bundle".');
        }

        return $this->container->get('twig')->render($view, $parameters);
    }

    /**
     * Renders a view.
     * TODO
     */
    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        if ($this->container->has('templating')) {
            $content = $this->container->get('templating')->render($view, $parameters);
        } elseif ($this->container->has('twig')) {
            $content = $this->container->get('twig')->render($view, $parameters);
        } else {
            throw new \LogicException(
                'You can not use the "render" method if the Templating Component or the Twig Bundle are not available. Try running "composer require twig/twig".');
        }

        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }

    /**
     * Streams a view.
     *
     * @
     */
    protected function stream(string $view, array $parameters = array(), StreamedResponse $response = null): StreamedResponse
    {
        if ($this->container->has('templating')) {
            $templating = $this->container->get('templating');

            $callback = function () use ($templating, $view, $parameters) {
                $templating->stream($view, $parameters);
            };
        } elseif ($this->container->has('twig')) {
            $twig = $this->container->get('twig');

            $callback = function () use ($twig, $view, $parameters) {
                $twig->display($view, $parameters);
            };
        } else {
            throw new \LogicException(
                'You can not use the "stream" method if the Templating Component or the Twig Bundle are not available. Try running "composer require symfony/twig-bundle".');
        }

        if (null === $response) {
            return new StreamedResponse($callback);
        }

        $response->setCallback($callback);

        return $response;
    }

    // /**
    // * Returns a NotFoundHttpException.
    // *
    // * This will result in a 404 response code. Usage example:
    // *
    // * throw $this->createNotFoundException('Page not found!');
    // */
    // protected function createNotFoundException(string $message = 'Not Found', \Exception $previous = null): NotFoundHttpException
    // {
    // return new NotFoundHttpException($message, $previous);
    // }

    // /**
    // * Returns an AccessDeniedException.
    // *
    // * This will result in a 403 response code. Usage example:
    // *
    // * throw $this->createAccessDeniedException('Unable to access this page!');
    // *
    // * @throws \LogicException If the Security component is not available
    // */
    // protected function createAccessDeniedException(string $message = 'Access Denied.', \Exception $previous = null): AccessDeniedException
    // {
    // if (! class_exists(AccessDeniedException::class)) {
    // throw new \LogicException(
    // 'You can not use the "createAccessDeniedException" method if the Security component is not available. Try running "composer require symfony/security-bundle".');
    // }

    // return new AccessDeniedException($message, $previous);
    // }

    // /**
    // * Creates and returns a Form instance from the type of the form.
    // */
    // protected function createForm(string $type, $data = null, array $options = array()): FormInterface
    // {
    // return $this->container->get('form.factory')->create($type, $data, $options);
    // }

    // /**
    // * Creates and returns a form builder instance.
    // */
    // protected function createFormBuilder($data = null, array $options = array()): FormBuilderInterface
    // {
    // return $this->container->get('form.factory')->createBuilder(FormType::class, $data, $options);
    // }

    // /**
    // * Shortcut to return the Doctrine Registry service.
    // *
    // * @throws \LogicException If DoctrineBundle is not available
    // */
    // protected function getDoctrine(): ManagerRegistry
    // {
    // if (! $this->container->has('doctrine')) {
    // throw new \LogicException(
    // 'The DoctrineBundle is not registered in your application. Try running "composer require symfony/orm-pack".');
    // }

    // return $this->container->get('doctrine');
    // }

    // /**
    // * Get a user from the Security Token Storage.
    // *
    // * @return mixed
    // *
    // * @throws \LogicException If SecurityBundle is not available
    // *
    // * @see TokenInterface::getUser()
    // */
    // protected function getUser()
    // {
    // if (! $this->container->has('security.token_storage')) {
    // throw new \LogicException(
    // 'The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
    // }

    // if (null === $token = $this->container->get('security.token_storage')->getToken()) {
    // return;
    // }

    // if (! \is_object($user = $token->getUser())) {
    // // e.g. anonymous authentication
    // return;
    // }

    // return $user;
    // }

    // /**
    // * Checks the validity of a CSRF token.
    // *
    // * @param string $id
    // * The id used when generating the token
    // * @param string|null $token
    // * The actual token sent with the request that should be validated
    // */
    // protected function isCsrfTokenValid(string $id, ?string $token): bool
    // {
    // if (! $this->container->has('security.csrf.token_manager')) {
    // throw new \LogicException(
    // 'CSRF protection is not enabled in your application. Enable it with the "csrf_protection" key in "config/packages/framework.yaml".');
    // }

    // return $this->container->get('security.csrf.token_manager')->isTokenValid(new CsrfToken($id, $token));
    // }

    // /**
    // * Dispatches a message to the bus.
    // *
    // * @param object|Envelope $message
    // * The message or the message pre-wrapped in an envelope
    // */
    // protected function dispatchMessage($message): Envelope
    // {
    // if (! $this->container->has('message_bus')) {
    // $message = class_exists(Envelope::class) ? 'You need to define the "messenger.default_bus" configuration option.' : 'Try running "composer require symfony/messenger".';
    // throw new \LogicException('The message bus is not enabled in your application. ' . $message);
    // }

    // return $this->container->get('message_bus')->dispatch($message);
    // }

    // /**
    // * Adds a Link HTTP header to the current response.
    // *
    // * @see https://tools.ietf.org/html/rfc5988
    // */
    // protected function addLink(Request $request, Link $link)
    // {
    // if (! class_exists(AddLinkHeaderListener::class)) {
    // throw new \LogicException(
    // 'You can not use the "addLink" method if the WebLink component is not available. Try running "composer require symfony/web-link".');
    // }

    // if (null === $linkProvider = $request->attributes->get('_links')) {
    // $request->attributes->set('_links', new GenericLinkProvider(array(
    // $link
    // )));

    // return;
    // }

    // $request->attributes->set('_links', $linkProvider->withLink($link));
    // }
}
