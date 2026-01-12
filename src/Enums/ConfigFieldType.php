<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration\Enums;

enum ConfigFieldType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case Select = 'select';
    case MultiSelect = 'multiselect';
    case Toggle = 'toggle';
    case Number = 'number';
    case Email = 'email';
    case Password = 'password';
    case Color = 'color';
    case ColorPalette = 'colorpalette';
    case Image = 'image';
    case RichEditor = 'richeditor';
    case Info = 'info';

    public function isArrayType(): bool
    {
        return $this === self::MultiSelect;
    }

    public function isReadOnly(): bool
    {
        return $this === self::Info;
    }
}
