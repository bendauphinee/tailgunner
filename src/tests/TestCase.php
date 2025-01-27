<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreateTestDb;

abstract class TestCase extends BaseTestCase
{
    use CreateTestDb;

    /**
     * Assert that an array has the specified keys.
     */
    protected function assertArrayHasKeys(array $keys, array $array): void
    {
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $array);
        }
    }
}
