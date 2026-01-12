<?php

declare(strict_types=1);

use Skylence\FilamentSystemConfiguration\Config\ConfigField;
use Skylence\FilamentSystemConfiguration\Config\ConfigGroup;

it('can be instantiated with make', function (): void {
    $group = ConfigGroup::make('test_group');

    expect($group)->toBeInstanceOf(ConfigGroup::class);
});

it('can get key', function (): void {
    $group = ConfigGroup::make('my_group');

    expect($group->getKey())->toBe('my_group');
});

it('can set and get label', function (): void {
    $group = ConfigGroup::make('group')->label('My Group');

    expect($group->getLabel())->toBe('My Group');
});

it('generates label from key when not set', function (): void {
    $group = ConfigGroup::make('my_group_name');

    expect($group->getLabel())->toBe('My Group Name');
});

it('can set and get icon', function (): void {
    $group = ConfigGroup::make('group')->icon('heroicon-o-cog');

    expect($group->getIcon())->toBe('heroicon-o-cog');
});

it('returns null for icon when not set', function (): void {
    $group = ConfigGroup::make('group');

    expect($group->getIcon())->toBeNull();
});

it('can set and get description', function (): void {
    $group = ConfigGroup::make('group')->description('Group description');

    expect($group->getDescription())->toBe('Group description');
});

it('returns null for description when not set', function (): void {
    $group = ConfigGroup::make('group');

    expect($group->getDescription())->toBeNull();
});

it('can set and get sort order', function (): void {
    $group = ConfigGroup::make('group')->sort(5);

    expect($group->getSort())->toBe(5);
});

it('defaults to sort 0', function (): void {
    $group = ConfigGroup::make('group');

    expect($group->getSort())->toBe(0);
});

it('is collapsible by default', function (): void {
    $group = ConfigGroup::make('group');

    expect($group->isCollapsible())->toBeTrue();
});

it('can disable collapsible', function (): void {
    $group = ConfigGroup::make('group')->collapsible(false);

    expect($group->isCollapsible())->toBeFalse();
});

it('is not collapsed by default', function (): void {
    $group = ConfigGroup::make('group');

    expect($group->isCollapsed())->toBeFalse();
});

it('can set collapsed', function (): void {
    $group = ConfigGroup::make('group')->collapsed();

    expect($group->isCollapsed())->toBeTrue();
});

it('can set fields', function (): void {
    $fields = [
        ConfigField::make('field1')->label('Field 1'),
        ConfigField::make('field2')->label('Field 2'),
    ];

    $group = ConfigGroup::make('group')->fields($fields);

    expect($group->getFields())->toHaveCount(2);
});

it('returns empty array when no fields', function (): void {
    $group = ConfigGroup::make('group');

    expect($group->getFields())->toBe([]);
});

it('filters out invisible fields', function (): void {
    $fields = [
        ConfigField::make('visible')->label('Visible'),
        ConfigField::make('hidden')->label('Hidden')->visible(fn (): bool => false),
    ];

    $group = ConfigGroup::make('group')->fields($fields);
    $resultFields = $group->getFields();

    expect($resultFields)->toHaveCount(1)
        ->and($resultFields[0]->getKey())->toBe('visible');
});

it('sorts fields by sort order', function (): void {
    $fields = [
        ConfigField::make('third')->sort(3),
        ConfigField::make('first')->sort(1),
        ConfigField::make('second')->sort(2),
    ];

    $group = ConfigGroup::make('group')->fields($fields);
    $resultFields = $group->getFields();

    expect($resultFields[0]->getKey())->toBe('first')
        ->and($resultFields[1]->getKey())->toBe('second')
        ->and($resultFields[2]->getKey())->toBe('third');
});

it('is visible by default', function (): void {
    $group = ConfigGroup::make('group');

    expect($group->isVisible())->toBeTrue();
});

it('can set visibility with closure returning true', function (): void {
    $group = ConfigGroup::make('group')->visible(fn (): bool => true);

    expect($group->isVisible())->toBeTrue();
});

it('can set visibility with closure returning false', function (): void {
    $group = ConfigGroup::make('group')->visible(fn (): bool => false);

    expect($group->isVisible())->toBeFalse();
});

it('can get field paths', function (): void {
    $fields = [
        ConfigField::make('field1'),
        ConfigField::make('field2'),
    ];

    $group = ConfigGroup::make('general')->fields($fields);
    $paths = $group->getFieldPaths('settings');

    expect($paths)->toBe([
        'settings/general/field1',
        'settings/general/field2',
    ]);
});

it('supports fluent interface', function (): void {
    $group = ConfigGroup::make('my_group')
        ->label('My Group')
        ->icon('heroicon-o-cog')
        ->description('Description')
        ->sort(1)
        ->collapsible()
        ->collapsed();

    expect($group)
        ->getKey()->toBe('my_group')
        ->getLabel()->toBe('My Group')
        ->getIcon()->toBe('heroicon-o-cog')
        ->getDescription()->toBe('Description')
        ->getSort()->toBe(1)
        ->isCollapsible()->toBeTrue()
        ->isCollapsed()->toBeTrue();
});

it('can convert to array', function (): void {
    $fields = [
        ConfigField::make('field1')->label('Field 1'),
    ];

    $group = ConfigGroup::make('test')
        ->label('Test Group')
        ->icon('heroicon-o-cog')
        ->description('Description')
        ->sort(2)
        ->collapsible()
        ->collapsed()
        ->fields($fields);

    $array = $group->toArray();

    expect($array)->toBeArray()
        ->and($array['key'])->toBe('test')
        ->and($array['label'])->toBe('Test Group')
        ->and($array['icon'])->toBe('heroicon-o-cog')
        ->and($array['description'])->toBe('Description')
        ->and($array['sort'])->toBe(2)
        ->and($array['collapsible'])->toBeTrue()
        ->and($array['collapsed'])->toBeTrue()
        ->and($array['fields'])->toHaveCount(1);
});
