<?php

declare(strict_types=1);

use Skylence\FilamentSystemConfiguration\FilamentSystemConfigurationPlugin;

it('can get plugin id', function (): void {
    $plugin = new FilamentSystemConfigurationPlugin;

    expect($plugin->getId())->toBe('filament-system-configuration');
});

it('can register sections', function (): void {
    $plugin = new FilamentSystemConfigurationPlugin;
    $plugin->sections(['App\Config\TestSection']);

    expect($plugin->getSections())->toBe(['App\Config\TestSection']);
});

it('returns empty array when no sections registered', function (): void {
    $plugin = new FilamentSystemConfigurationPlugin;

    expect($plugin->getSections())->toBe([]);
});

it('can set sidebar groups', function (): void {
    $groups = [
        'General' => ['settings', 'appearance'],
        'Advanced' => ['api', 'security'],
    ];

    $plugin = new FilamentSystemConfigurationPlugin;
    $plugin->sidebarGroups($groups);

    expect($plugin->getSidebarGroups())->toBe($groups);
});

it('returns empty array when no sidebar groups set', function (): void {
    $plugin = new FilamentSystemConfigurationPlugin;

    expect($plugin->getSidebarGroups())->toBe([]);
});

it('can disable page registration', function (): void {
    $plugin = new FilamentSystemConfigurationPlugin;
    $result = $plugin->registerPage(false);

    expect($result)->toBe($plugin);
});

it('supports fluent interface', function (): void {
    $plugin = (new FilamentSystemConfigurationPlugin)
        ->registerPage(true)
        ->sections(['App\Config\Section1'])
        ->sidebarGroups(['Group' => ['section1']]);

    expect($plugin)->toBeInstanceOf(FilamentSystemConfigurationPlugin::class)
        ->and($plugin->getSections())->toBe(['App\Config\Section1'])
        ->and($plugin->getSidebarGroups())->toBe(['Group' => ['section1']]);
});
