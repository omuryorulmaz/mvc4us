<?php
namespace Mvc4us\Tests;

use Mvc4us\Controller\AbstractController;
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

            public function handle(\Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\HttpFoundation\Response $response)
            {}
        };
    }

    public function testHas()
    {}
}

