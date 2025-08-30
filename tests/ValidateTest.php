<?php

namespace Tests;

use AsymmetricJWTKit\Validator;
use PHPUnit\Framework\TestCase;

class ValidateTest extends TestCase
{
    public function testEncode()
    {
        $token = file_get_contents(__DIR__.'/fixtures/token.txt');

        $token = (new Validator())->validate($token);

        $this->assertTrue(true);
    }
}