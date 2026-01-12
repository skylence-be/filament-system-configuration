<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration\Concerns;

use Skylence\FilamentContextSidebar\ContextNavigationItem;
use Skylence\FilamentContextSidebar\ContextSidebar;
use Skylence\FilamentSystemConfiguration\FilamentSystemConfigurationPlugin;
use Skylence\FilamentSystemConfiguration\SystemConfig;

trait HasConfigurationSidebar
{
    public static function sidebar(): ContextSidebar
    {
        $currentSection = request()->query('section', 'general');

        // Get sidebar groups from plugin configuration
        $sidebarGroups = static::getSidebarGroups();

        $items = [];

        foreach ($sidebarGroups as $groupName => $sectionKeys) {
            $isFirstInGroup = true;

            foreach ($sectionKeys as $sectionKey) {
                $section = SystemConfig::getSection($sectionKey);

                if (! $section) {
                    continue;
                }

                $items[] = ContextNavigationItem::make($section->getLabel())
                    ->icon($isFirstInGroup ? $section->getIcon() : null)
                    ->url(static::getUrl(['section' => $section->getKey()]))
                    ->group($groupName)
                    ->isActiveWhen(fn (): bool => $currentSection === $section->getKey());

                $isFirstInGroup = false;
            }
        }

        /** @var string $sidebarTitle */
        $sidebarTitle = config('filament-system-configuration.sidebar.title', 'Configuration');
        /** @var string $sidebarDescription */
        $sidebarDescription = config('filament-system-configuration.sidebar.description', 'System Settings');

        return ContextSidebar::make()
            ->title($sidebarTitle)
            ->description($sidebarDescription)
            ->navigationItems($items);
    }

    /**
     * Get the sidebar groups configuration.
     *
     * @return array<string, array<string>>
     */
    protected static function getSidebarGroups(): array
    {
        // First try to get from plugin configuration
        try {
            $plugin = FilamentSystemConfigurationPlugin::get();
            $groups = $plugin->getSidebarGroups();

            if ($groups !== []) {
                return $groups;
            }
        } catch (\Exception) {
            // Plugin not registered, use config fallback
        }

        // Fallback to config file
        /** @var array<string, array<string>>|null $configGroups */
        $configGroups = config('filament-system-configuration.sidebar.groups');

        if (! empty($configGroups)) {
            return $configGroups;
        }

        // Default: auto-generate from registered sections
        return static::generateSidebarGroupsFromSections();
    }

    /**
     * Generate sidebar groups from registered sections.
     *
     * @return array<string, array<string>>
     */
    protected static function generateSidebarGroupsFromSections(): array
    {
        $sections = SystemConfig::getSections();
        $groups = ['Configuration' => []];

        foreach ($sections as $section) {
            $groups['Configuration'][] = $section->getKey();
        }

        return $groups;
    }
}
