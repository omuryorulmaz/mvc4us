<?php
namespace Mvc4us\Controller;

use Mvc4us\Http\Request;
use Mvc4us\Http\Response;

interface ControllerInterface
{

    /**
     * Handle request.
     *
     * @param \Mvc4us\Http\Request $request
     * @param \Mvc4us\Http\Response $response
     */
    function handle(Request $request, Response $response);
}
