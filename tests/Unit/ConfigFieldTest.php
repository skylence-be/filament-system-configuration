<?php

declare(strict_types=1);

use Skylence\FilamentSystemConfiguration\Config\ConfigField;
use Skylence\FilamentSystemConfiguration\Enums\ConfigFieldType;

it('can be instantiated with make', function (): void {
    $field = ConfigField::make('test_field');

    expect($field)->toBeInstanceOf(ConfigField::class);
});

it('can get key', function (): void {
    $field = ConfigField::make('my_field');

    expect($field->getKey())->toBe('my_field');
});

it('can set and get label', function (): void {
    $field = ConfigField::make('field')->label('My Label');

    expect($field->getLabel())->toBe('My Label');
});

it('generates label from key when not set', function (): void {
    $field = ConfigField::make('my_field_name');

    expect($field->getLabel())->toBe('My Field Name');
});

it('defaults to text type', function (): void {
    $field = ConfigField::make('field');

    expect($field->getType())->toBe(ConfigFieldType::Text);
});

it('can set type directly', function (): void {
    $field = ConfigField::make('field')->type(ConfigFieldType::Toggle);

    expect($field->getType())->toBe(ConfigFieldType::Toggle);
});

it('can use text shorthand', function (): void {
    $field = ConfigField::make('field')->text();

    expect($field->getType())->toBe(ConfigFieldType::Text);
});

it('can use textarea shorthand', function (): void {
    $field = ConfigField::make('field')->textarea();

    expect($field->getType())->toBe(ConfigFieldType::Textarea);
});

it('can use toggle shorthand', function (): void {
    $field = ConfigField::make('field')->toggle();

    expect($field->getType())->toBe(ConfigFieldType::Toggle);
});

it('can use number shorthand', function (): void {
    $field = ConfigField::make('field')->number();

    expect($field->getType())->toBe(ConfigFieldType::Number);
});

it('can use email shorthand', function (): void {
    $field = ConfigField::make('field')->email();

    expect($field->getType())->toBe(ConfigFieldType::Email);
});

it('can use password shorthand', function (): void {
    $field = ConfigField::make('field')->password();

    expect($field->getType())->toBe(ConfigFieldType::Password);
});

it('can use color shorthand', function (): void {
    $field = ConfigField::make('field')->color();

    expect($field->getType())->toBe(ConfigFieldType::Color);
});

it('can use image shorthand', function (): void {
    $field = ConfigField::make('field')->image();

    expect($field->getType())->toBe(ConfigFieldType::Image);
});

it('can use richEditor shorthand', function (): void {
    $field = ConfigField::make('field')->richEditor();

    expect($field->getType())->toBe(ConfigFieldType::RichEditor);
});

it('can use select with options', function (): void {
    $options = ['a' => 'Option A', 'b' => 'Option B'];
    $field = ConfigField::make('field')->select($options);

    expect($field->getType())->toBe(ConfigFieldType::Select)
        ->and($field->getOptions())->toBe($options);
});

it('can use multiSelect with options', function (): void {
    $options = ['a' => 'Option A', 'b' => 'Option B'];
    $field = ConfigField::make('field')->multiSelect($options);

    expect($field->getType())->toBe(ConfigFieldType::MultiSelect)
        ->and($field->getOptions())->toBe($options);
});

it('can use colorPalette with options', function (): void {
    $options = ['red' => '#ff0000', 'blue' => '#0000ff'];
    $field = ConfigField::make('field')->colorPalette($options);

    expect($field->getType())->toBe(ConfigFieldType::ColorPalette)
        ->and($field->getOptions())->toBe($options);
});

it('can use info type', function (): void {
    $field = ConfigField::make('field')->info();

    expect($field->getType())->toBe(ConfigFieldType::Info);
});

it('can use info type with config key', function (): void {
    $field = ConfigField::make('field')->info('app.name');

    expect($field->getType())->toBe(ConfigFieldType::Info)
        ->and($field->getConfigKey())->toBe('app.name');
});

it('can set and get default value', function (): void {
    $field = ConfigField::make('field')->default('default_value');

    expect($field->getDefault())->toBe('default_value');
});

it('returns null for default when not set', function (): void {
    $field = ConfigField::make('field');

    expect($field->getDefault())->toBeNull();
});

it('can set and get helper text', function (): void {
    $field = ConfigField::make('field')->helperText('This is helpful');

    expect($field->getHelperText())->toBe('This is helpful');
});

it('returns null for helper text when not set', function (): void {
    $field = ConfigField::make('field');

    expect($field->getHelperText())->toBeNull();
});

it('can set and get placeholder', function (): void {
    $field = ConfigField::make('field')->placeholder('Enter value...');

    expect($field->getPlaceholder())->toBe('Enter value...');
});

it('returns null for placeholder when not set', function (): void {
    $field = ConfigField::make('field');

    expect($field->getPlaceholder())->toBeNull();
});

it('can set options separately', function (): void {
    $options = ['x' => 'X Value', 'y' => 'Y Value'];
    $field = ConfigField::make('field')->options($options);

    expect($field->getOptions())->toBe($options);
});

it('can set options with closure', function (): void {
    $field = ConfigField::make('field')->options(fn (): array => ['a' => 'A', 'b' => 'B']);

    expect($field->getOptions())->toBe(['a' => 'A', 'b' => 'B']);
});

it('returns empty array when no options set', function (): void {
    $field = ConfigField::make('field');

    expect($field->getOptions())->toBe([]);
});

it('can set and get rules', function (): void {
    $rules = ['required', 'string', 'max:255'];
    $field = ConfigField::make('field')->rules($rules);

    expect($field->getRules())->toBe($rules);
});

it('returns empty array for rules when not set', function (): void {
    $field = ConfigField::make('field');

    expect($field->getRules())->toBe([]);
});

it('can set required', function (): void {
    $field = ConfigField::make('field')->required();

    expect($field->isRequired())->toBeTrue();
});

it('can set required to false', function (): void {
    $field = ConfigField::make('field')->required(false);

    expect($field->isRequired())->toBeFalse();
});

it('defaults to not required', function (): void {
    $field = ConfigField::make('field');

    expect($field->isRequired())->toBeFalse();
});

it('can set and get sort order', function (): void {
    $field = ConfigField::make('field')->sort(5);

    expect($field->getSort())->toBe(5);
});

it('defaults to sort 0', function (): void {
    $field = ConfigField::make('field');

    expect($field->getSort())->toBe(0);
});

it('can set encrypted', function (): void {
    $field = ConfigField::make('field')->encrypted();

    expect($field->isEncrypted())->toBeTrue();
});

it('can set encrypted to false', function (): void {
    $field = ConfigField::make('field')->encrypted(false);

    expect($field->isEncrypted())->toBeFalse();
});

it('defaults to not encrypted', function (): void {
    $field = ConfigField::make('field');

    expect($field->isEncrypted())->toBeFalse();
});

it('can set system value option', function (): void {
    $field = ConfigField::make('field')->systemValueOption(false);

    expect($field->hasSystemValueOption())->toBeFalse();
});

it('defaults to having system value option', function (): void {
    $field = ConfigField::make('field');

    expect($field->hasSystemValueOption())->toBeTrue();
});

it('can set config key', function (): void {
    $field = ConfigField::make('field')->configKey('app.debug');

    expect($field->getConfigKey())->toBe('app.debug');
});

it('returns null for config key when not set', function (): void {
    $field = ConfigField::make('field');

    expect($field->getConfigKey())->toBeNull();
});

it('is visible by default', function (): void {
    $field = ConfigField::make('field');

    expect($field->isVisible())->toBeTrue();
});

it('can set visibility with closure returning true', function (): void {
    $field = ConfigField::make('field')->visible(fn (): bool => true);

    expect($field->isVisible())->toBeTrue();
});

it('can set visibility with closure returning false', function (): void {
    $field = ConfigField::make('field')->visible(fn (): bool => false);

    expect($field->isVisible())->toBeFalse();
});

it('can set used in code flag', function (): void {
    $field = ConfigField::make('field')->usedInCode();

    expect($field->isUsedInCode())->toBeTrue();
});

it('defaults to not used in code', function (): void {
    $field = ConfigField::make('field');

    expect($field->isUsedInCode())->toBeFalse();
});

it('supports fluent interface', function (): void {
    $field = ConfigField::make('my_field')
        ->label('My Field')
        ->type(ConfigFieldType::Text)
        ->default('default')
        ->helperText('Help')
        ->placeholder('Enter...')
        ->rules(['required'])
        ->required()
        ->sort(1)
        ->encrypted()
        ->usedInCode();

    expect($field)
        ->getKey()->toBe('my_field')
        ->getLabel()->toBe('My Field')
        ->getType()->toBe(ConfigFieldType::Text)
        ->getDefault()->toBe('default')
        ->getHelperText()->toBe('Help')
        ->getPlaceholder()->toBe('Enter...')
        ->getRules()->toBe(['required'])
        ->isRequired()->toBeTrue()
        ->getSort()->toBe(1)
        ->isEncrypted()->toBeTrue()
        ->isUsedInCode()->toBeTrue();
});

it('can convert to array', function (): void {
    $field = ConfigField::make('test')
        ->label('Test Field')
        ->type(ConfigFieldType::Text)
        ->default('value')
        ->helperText('Help text')
        ->placeholder('Enter value')
        ->required()
        ->sort(2)
        ->encrypted()
        ->usedInCode();

    $array = $field->toArray();

    expect($array)->toBeArray()
        ->and($array['key'])->toBe('test')
        ->and($array['label'])->toBe('Test Field')
        ->and($array['type'])->toBe('text')
        ->and($array['default'])->toBe('value')
        ->and($array['helperText'])->toBe('Help text')
        ->and($array['placeholder'])->toBe('Enter value')
        ->and($array['required'])->toBeTrue()
        ->and($array['sort'])->toBe(2)
        ->and($array['encrypted'])->toBeTrue()
        ->and($array['usedInCode'])->toBeTrue();
});
