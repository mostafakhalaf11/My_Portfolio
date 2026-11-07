<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * A cached configuration ignores phpunit.xml, so tests would run against the development
     * database and RefreshDatabase would wipe it with migrate:fresh. Refuse to start instead.
     */
    protected function setUp(): void
    {
        if (is_file(dirname(__DIR__).'/bootstrap/cache/config.php')) {
            $this->fail('Configuration is cached. Run `php artisan config:clear` first: cached config bypasses phpunit.xml and RefreshDatabase would wipe the development database.');
        }

        parent::setUp();
    }
}
