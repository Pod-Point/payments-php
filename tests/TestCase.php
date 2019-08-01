<?php

namespace PodPoint\Payments\Tests;

use Dotenv\Dotenv;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Loads environment variables.
     */
    protected function setUp()
    {
        parent::setUp();

        $dotenv = Dotenv::create(__DIR__ . '/..');
        $dotenv->load();
    }
}
