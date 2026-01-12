<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration;

use Skylence\FilamentSystemConfiguration\Services\ConfigService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSystemConfigurationServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-system-configuration';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_config_values_table');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ConfigService::class, fn (): ConfigService => new ConfigService);
    }

    public function packageBooted(): void
    {
        //
    }
}
