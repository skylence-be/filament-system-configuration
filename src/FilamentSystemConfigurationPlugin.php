<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Skylence\FilamentSystemConfiguration\Pages\SystemConfiguration;

class FilamentSystemConfigurationPlugin implements Plugin
{
    protected bool $registerPage = true;

    /** @var array<class-string> */
    protected array $sections = [];

    /** @var array<string, array<string>> */
    protected array $sidebarGroups = [];

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'filament-system-configuration';
    }

    public function registerPage(bool $register = true): static
    {
        $this->registerPage = $register;

        return $this;
    }

    /**
     * Register configuration section classes.
     *
     * @param  array<class-string>  $sections
     */
    public function sections(array $sections): static
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     * @return array<class-string>
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * Define sidebar navigation groups.
     *
     * @param  array<string, array<string>>  $groups
     */
    public function sidebarGroups(array $groups): static
    {
        $this->sidebarGroups = $groups;

        return $this;
    }

    /**
     * @return array<string, array<string>>
     */
    public function getSidebarGroups(): array
    {
        return $this->sidebarGroups;
    }

    public function register(Panel $panel): void
    {
        if ($this->registerPage) {
            $panel->pages([
                SystemConfiguration::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        // Register sections
        foreach ($this->sections as $sectionClass) {
            if (method_exists($sectionClass, 'make')) {
                SystemConfig::register($sectionClass::make());
            }
        }
    }
}
