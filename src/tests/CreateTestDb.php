<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Console\Kernel;

trait CreateTestDb
{
    protected function setUp(): void
    {
        parent::setUp();

        // Run once per test suite
        static $dbInitialized = false;

        if (!$dbInitialized) {
            Artisan::call('migrate:fresh');
            $dbInitialized = true;
        }
    }
}