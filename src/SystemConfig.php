<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration;

use Skylence\FilamentSystemConfiguration\Config\ConfigField;
use Skylence\FilamentSystemConfiguration\Config\ConfigSection;
use Skylence\FilamentSystemConfiguration\Config\Registries\ConfigRegistry;

/**
 * Static facade for the System Configuration registry.
 *
 * @method static ConfigRegistry register(ConfigSection $section)
 * @method static ConfigRegistry registerMany(array<ConfigSection> $sections)
 * @method static ConfigSection|null getSection(string $key)
 * @method static array<ConfigSection> getSections()
 * @method static array<string> getFieldPaths()
 * @method static ConfigField|null getFieldByPath(string $path)
 * @method static bool hasSection(string $key)
 */
class SystemConfig
{
    /**
     * Get the config registry instance.
     */
    public static function getRegistry(): ConfigRegistry
    {
        return ConfigRegistry::getInstance();
    }

    /**
     * Register a configuration section.
     */
    public static function register(ConfigSection $section): ConfigRegistry
    {
        return self::getRegistry()->register($section);
    }

    /**
     * Register multiple configuration sections.
     *
     * @param  array<ConfigSection>  $sections
     */
    public static function registerMany(array $sections): ConfigRegistry
    {
        return self::getRegistry()->registerMany($sections);
    }

    /**
     * Get a specific section by key.
     */
    public static function getSection(string $key): ?ConfigSection
    {
        return self::getRegistry()->getSection($key);
    }

    /**
     * Get all registered sections.
     *
     * @return array<ConfigSection>
     */
    public static function getSections(): array
    {
        return self::getRegistry()->getSections();
    }

    /**
     * Get all field paths from all registered sections.
     *
     * @return array<string>
     */
    public static function getFieldPaths(): array
    {
        return self::getRegistry()->getFieldPaths();
    }

    /**
     * Get a field definition by its full path.
     */
    public static function getFieldByPath(string $path): ?ConfigField
    {
        return self::getRegistry()->getFieldByPath($path);
    }

    /**
     * Check if a section exists.
     */
    public static function hasSection(string $key): bool
    {
        return self::getRegistry()->hasSection($key);
    }
}
