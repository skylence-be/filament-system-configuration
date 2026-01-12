<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration\Config\Registries;

use Skylence\FilamentSystemConfiguration\Config\ConfigField;
use Skylence\FilamentSystemConfiguration\Config\ConfigSection;

class ConfigRegistry
{
    private static ?ConfigRegistry $instance = null;

    /** @var array<string, ConfigSection> */
    protected array $sections = [];

    private function __construct()
    {
        // Private constructor for singleton
    }

    public static function getInstance(): ConfigRegistry
    {
        if (! self::$instance instanceof ConfigRegistry) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register a configuration section.
     */
    public function register(ConfigSection $section): static
    {
        $this->sections[$section->getKey()] = $section;

        return $this;
    }

    /**
     * Register multiple configuration sections.
     *
     * @param  array<ConfigSection>  $sections
     */
    public function registerMany(array $sections): static
    {
        foreach ($sections as $section) {
            $this->register($section);
        }

        return $this;
    }

    /**
     * Get a specific section by key.
     */
    public function getSection(string $key): ?ConfigSection
    {
        return $this->sections[$key] ?? null;
    }

    /**
     * Get all registered sections.
     *
     * @return array<ConfigSection>
     */
    public function getSections(): array
    {
        $sections = array_filter($this->sections, fn (ConfigSection $section): bool => $section->isVisible());

        usort($sections, fn (ConfigSection $a, ConfigSection $b): int => $a->getSort() <=> $b->getSort());

        return $sections;
    }

    /**
     * Get all field paths from all registered sections.
     *
     * @return array<string>
     */
    public function getFieldPaths(): array
    {
        $paths = [];

        foreach ($this->getSections() as $section) {
            $paths = array_merge($paths, $section->getFieldPaths());
        }

        return $paths;
    }

    /**
     * Get a field definition by its full path.
     */
    public function getFieldByPath(string $path): ?ConfigField
    {
        $parts = explode('/', $path);

        if (count($parts) !== 3) {
            return null;
        }

        [$sectionKey, $groupKey, $fieldKey] = $parts;

        $section = $this->getSection($sectionKey);

        if (! $section instanceof ConfigSection) {
            return null;
        }

        return $section->getField($groupKey, $fieldKey);
    }

    /**
     * Check if a section exists.
     */
    public function hasSection(string $key): bool
    {
        return isset($this->sections[$key]);
    }

    /**
     * Clear all registered sections (useful for testing).
     */
    public function clear(): static
    {
        $this->sections = [];

        return $this;
    }

    /**
     * Reset the singleton instance (useful for testing).
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
