<?php

namespace AsymmetricJWTKit\Tests;

use AsymmetricJWTKit\Config;
use AsymmetricJWTKit\Encode;
use Orchestra\Testbench\TestCase;

class EncodeTest extends TestCase
{
    public function testEncode()
    {
        $config = new Config();

        $token = (new Encode($config))->createToken();

        $this->assertEquals(file_get_contents('/app/tests/fixtures/token.txt'), $token);
    }
}