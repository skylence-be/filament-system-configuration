<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Skylence\FilamentSystemConfiguration\FilamentSystemConfigurationServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FilamentSystemConfigurationServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }
}
