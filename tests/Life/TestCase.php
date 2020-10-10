<?php declare(strict_types = 1);

namespace Life;

use Mockery;
use Tester\TestCase as TestTestCase;

abstract class TestCase extends TestTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }
}
