<?php
namespace Mvc4us\Http;

class Request extends \Symfony\Component\HttpFoundation\Request
{

    public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    public static function createFromSymfonyRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        return new Request(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all(),
            $request->getContent());
    }
}
