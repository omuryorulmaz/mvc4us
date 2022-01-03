<?php

declare(strict_types=1);

namespace Mvc4us\Tests;

use Mvc4us\Controller\AbstractController;
use PHPUnit\Framework\TestCase;

class AbstractControllerTest extends TestCase
{

    protected $abstractController;

    protected function setup(): void
    {
        $this->abstractController = new class() extends AbstractController {

            public function returnThis(): self
            {
                return $this;
            }

            public function handle(\Symfony\Component\HttpFoundation\Request $request
            ): \Symfony\Component\HttpFoundation\Response {
                return new  \Symfony\Component\HttpFoundation\Response("hello testing");
            }
        };
    }

    public function testHas()
    {
    }
}

