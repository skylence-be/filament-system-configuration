<?php

declare(strict_types=1);

use Skylence\FilamentSystemConfiguration\Enums\ConfigFieldType;

it('has text type', function (): void {
    expect(ConfigFieldType::Text->value)->toBe('text');
});

it('has textarea type', function (): void {
    expect(ConfigFieldType::Textarea->value)->toBe('textarea');
});

it('has select type', function (): void {
    expect(ConfigFieldType::Select->value)->toBe('select');
});

it('has multiselect type', function (): void {
    expect(ConfigFieldType::MultiSelect->value)->toBe('multiselect');
});

it('has toggle type', function (): void {
    expect(ConfigFieldType::Toggle->value)->toBe('toggle');
});

it('has number type', function (): void {
    expect(ConfigFieldType::Number->value)->toBe('number');
});

it('has email type', function (): void {
    expect(ConfigFieldType::Email->value)->toBe('email');
});

it('has password type', function (): void {
    expect(ConfigFieldType::Password->value)->toBe('password');
});

it('has color type', function (): void {
    expect(ConfigFieldType::Color->value)->toBe('color');
});

it('has colorpalette type', function (): void {
    expect(ConfigFieldType::ColorPalette->value)->toBe('colorpalette');
});

it('has image type', function (): void {
    expect(ConfigFieldType::Image->value)->toBe('image');
});

it('has richeditor type', function (): void {
    expect(ConfigFieldType::RichEditor->value)->toBe('richeditor');
});

it('has info type', function (): void {
    expect(ConfigFieldType::Info->value)->toBe('info');
});

it('identifies MultiSelect as array type', function (): void {
    expect(ConfigFieldType::MultiSelect->isArrayType())->toBeTrue();
});

it('identifies non-MultiSelect types as not array type', function (): void {
    expect(ConfigFieldType::Text->isArrayType())->toBeFalse();
    expect(ConfigFieldType::Select->isArrayType())->toBeFalse();
    expect(ConfigFieldType::Toggle->isArrayType())->toBeFalse();
});

it('identifies Info as read only', function (): void {
    expect(ConfigFieldType::Info->isReadOnly())->toBeTrue();
});

it('identifies non-Info types as not read only', function (): void {
    expect(ConfigFieldType::Text->isReadOnly())->toBeFalse();
    expect(ConfigFieldType::Select->isReadOnly())->toBeFalse();
    expect(ConfigFieldType::Toggle->isReadOnly())->toBeFalse();
});
