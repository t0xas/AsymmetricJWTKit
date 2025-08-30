<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getFixture(string $fixtureName): string
    {
        return file_get_contents(__DIR__. "/fixtures/{$fixtureName}");
    }
}