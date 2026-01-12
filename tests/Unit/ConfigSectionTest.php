<?php

declare(strict_types=1);

use Skylence\FilamentSystemConfiguration\Config\ConfigField;
use Skylence\FilamentSystemConfiguration\Config\ConfigGroup;
use Skylence\FilamentSystemConfiguration\Config\ConfigSection;

it('can be instantiated with make', function (): void {
    $section = ConfigSection::make('test_section');

    expect($section)->toBeInstanceOf(ConfigSection::class);
});

it('can get key', function (): void {
    $section = ConfigSection::make('my_section');

    expect($section->getKey())->toBe('my_section');
});

it('can set and get label', function (): void {
    $section = ConfigSection::make('section')->label('My Section');

    expect($section->getLabel())->toBe('My Section');
});

it('generates label from key when not set', function (): void {
    $section = ConfigSection::make('my_section_name');

    expect($section->getLabel())->toBe('My Section Name');
});

it('can set and get icon', function (): void {
    $section = ConfigSection::make('section')->icon('heroicon-o-cog');

    expect($section->getIcon())->toBe('heroicon-o-cog');
});

it('returns default icon when not set', function (): void {
    $section = ConfigSection::make('section');

    expect($section->getIcon())->toBe('heroicon-o-cog-6-tooth');
});

it('can set and get sort order', function (): void {
    $section = ConfigSection::make('section')->sort(5);

    expect($section->getSort())->toBe(5);
});

it('defaults to sort 0', function (): void {
    $section = ConfigSection::make('section');

    expect($section->getSort())->toBe(0);
});

it('can set groups', function (): void {
    $groups = [
        ConfigGroup::make('group1')->label('Group 1'),
        ConfigGroup::make('group2')->label('Group 2'),
    ];

    $section = ConfigSection::make('section')->groups($groups);

    expect($section->getGroups())->toHaveCount(2);
});

it('returns empty array when no groups', function (): void {
    $section = ConfigSection::make('section');

    expect($section->getGroups())->toBe([]);
});

it('filters out invisible groups', function (): void {
    $groups = [
        ConfigGroup::make('visible')->label('Visible'),
        ConfigGroup::make('hidden')->label('Hidden')->visible(fn (): bool => false),
    ];

    $section = ConfigSection::make('section')->groups($groups);
    $resultGroups = $section->getGroups();

    expect($resultGroups)->toHaveCount(1)
        ->and($resultGroups[0]->getKey())->toBe('visible');
});

it('sorts groups by sort order', function (): void {
    $groups = [
        ConfigGroup::make('third')->sort(3),
        ConfigGroup::make('first')->sort(1),
        ConfigGroup::make('second')->sort(2),
    ];

    $section = ConfigSection::make('section')->groups($groups);
    $resultGroups = $section->getGroups();

    expect($resultGroups[0]->getKey())->toBe('first')
        ->and($resultGroups[1]->getKey())->toBe('second')
        ->and($resultGroups[2]->getKey())->toBe('third');
});

it('can get group by key', function (): void {
    $groups = [
        ConfigGroup::make('group1')->label('Group 1'),
        ConfigGroup::make('group2')->label('Group 2'),
    ];

    $section = ConfigSection::make('section')->groups($groups);
    $group = $section->getGroup('group2');

    expect($group)->not->toBeNull()
        ->and($group->getKey())->toBe('group2');
});

it('returns null when group not found', function (): void {
    $section = ConfigSection::make('section')->groups([]);

    expect($section->getGroup('nonexistent'))->toBeNull();
});

it('is visible by default', function (): void {
    $section = ConfigSection::make('section');

    expect($section->isVisible())->toBeTrue();
});

it('can set visibility with closure returning true', function (): void {
    $section = ConfigSection::make('section')->visible(fn (): bool => true);

    expect($section->isVisible())->toBeTrue();
});

it('can set visibility with closure returning false', function (): void {
    $section = ConfigSection::make('section')->visible(fn (): bool => false);

    expect($section->isVisible())->toBeFalse();
});

it('can get field paths', function (): void {
    $groups = [
        ConfigGroup::make('general')->fields([
            ConfigField::make('field1'),
            ConfigField::make('field2'),
        ]),
        ConfigGroup::make('advanced')->fields([
            ConfigField::make('field3'),
        ]),
    ];

    $section = ConfigSection::make('settings')->groups($groups);
    $paths = $section->getFieldPaths();

    expect($paths)->toBe([
        'settings/general/field1',
        'settings/general/field2',
        'settings/advanced/field3',
    ]);
});

it('can get field by group and field key', function (): void {
    $groups = [
        ConfigGroup::make('general')->fields([
            ConfigField::make('app_name')->label('App Name'),
            ConfigField::make('debug')->label('Debug'),
        ]),
    ];

    $section = ConfigSection::make('settings')->groups($groups);
    $field = $section->getField('general', 'debug');

    expect($field)->not->toBeNull()
        ->and($field->getKey())->toBe('debug')
        ->and($field->getLabel())->toBe('Debug');
});

it('returns null when field not found', function (): void {
    $groups = [
        ConfigGroup::make('general')->fields([
            ConfigField::make('existing'),
        ]),
    ];

    $section = ConfigSection::make('settings')->groups($groups);

    expect($section->getField('general', 'nonexistent'))->toBeNull();
});

it('returns null when group not found for field lookup', function (): void {
    $section = ConfigSection::make('settings')->groups([]);

    expect($section->getField('nonexistent', 'field'))->toBeNull();
});

it('supports fluent interface', function (): void {
    $section = ConfigSection::make('my_section')
        ->label('My Section')
        ->icon('heroicon-o-home')
        ->sort(1);

    expect($section)
        ->getKey()->toBe('my_section')
        ->getLabel()->toBe('My Section')
        ->getIcon()->toBe('heroicon-o-home')
        ->getSort()->toBe(1);
});

it('can convert to array', function (): void {
    $groups = [
        ConfigGroup::make('group1')->label('Group 1'),
    ];

    $section = ConfigSection::make('test')
        ->label('Test Section')
        ->icon('heroicon-o-cog')
        ->sort(2)
        ->groups($groups);

    $array = $section->toArray();

    expect($array)->toBeArray()
        ->and($array['key'])->toBe('test')
        ->and($array['label'])->toBe('Test Section')
        ->and($array['icon'])->toBe('heroicon-o-cog')
        ->and($array['sort'])->toBe(2)
        ->and($array['groups'])->toHaveCount(1);
});
