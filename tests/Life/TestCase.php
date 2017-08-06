<?php

namespace Life;


abstract class TestCase extends \Tester\TestCase
{

    protected function tearDown()
    {
        parent::tearDown();
        \Mockery::close();
    }

}
