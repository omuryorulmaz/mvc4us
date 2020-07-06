<?php
namespace Mvc4us\Tests;

use Mvc4us\Controller\AbstractController;
use Mvc4us\Http\Request;
use Mvc4us\Http\Response;
use PHPUnit\Framework\TestCase;

class AbstractControllerTest extends TestCase
{

    protected $abstractController;

    protected function setup()
    {
        $this->abstractController = new class() extends AbstractController {

            public function returnThis()
            {
                return $this;
            }

            public function handle(Request $request, Response $response)
            {}
        };
    }

    public function testHas()
    {}
}

