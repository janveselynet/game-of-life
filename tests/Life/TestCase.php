<?php declare(strict_types=1);

namespace Life;


abstract class TestCase extends \Tester\TestCase
{

    protected function tearDown()
    {
        parent::tearDown();
        \Mockery::close();
    }

}
