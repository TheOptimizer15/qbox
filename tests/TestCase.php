<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * application base url /api/v1/
    */
    protected string $baseUrl =  '/api/v1/';
}
