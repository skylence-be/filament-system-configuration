<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration\Config\Sections;

use Skylence\FilamentSystemConfiguration\Config\ConfigField;
use Skylence\FilamentSystemConfiguration\Config\ConfigGroup;
use Skylence\FilamentSystemConfiguration\Config\ConfigSection;

/**
 * Example configuration section demonstrating available field types.
 *
 * Copy this file to your application and customize it:
 * - Move to app/Config/Sections/
 * - Update namespace to App\Config\Sections
 * - Modify fields, groups, and settings as needed
 */
class ExampleSection
{
    public static function make(): ConfigSection
    {
        return ConfigSection::make('example')
            ->label('Example Section')
            ->icon('heroicon-o-beaker')
            ->sort(100)
            ->groups([
                self::textFieldsGroup(),
                self::selectionFieldsGroup(),
                self::toggleFieldsGroup(),
                self::advancedFieldsGroup(),
            ]);
    }

    protected static function textFieldsGroup(): ConfigGroup
    {
        return ConfigGroup::make('text_fields')
            ->label('Text Fields')
            ->icon('heroicon-o-document-text')
            ->description('Examples of text-based input fields')
            ->fields([
                ConfigField::make('text_example')
                    ->label('Text Input')
                    ->text()
                    ->placeholder('Enter some text...')
                    ->helperText('A simple single-line text input.')
                    ->sort(10),

                ConfigField::make('textarea_example')
                    ->label('Textarea')
                    ->textarea()
                    ->placeholder('Enter longer text...')
                    ->helperText('A multi-line text area.')
                    ->sort(20),

                ConfigField::make('email_example')
                    ->label('Email')
                    ->email()
                    ->placeholder('email@example.com')
                    ->helperText('Email input with validation.')
                    ->sort(30),

                ConfigField::make('password_example')
                    ->label('Password')
                    ->password()
                    ->encrypted()
                    ->placeholder('Enter password')
                    ->helperText('Password field (value is encrypted).')
                    ->sort(40),

                ConfigField::make('number_example')
                    ->label('Number')
                    ->number()
                    ->default(100)
                    ->helperText('Numeric input field.')
                    ->sort(50),
            ]);
    }

    protected static function selectionFieldsGroup(): ConfigGroup
    {
        return ConfigGroup::make('selection_fields')
            ->label('Selection Fields')
            ->icon('heroicon-o-list-bullet')
            ->description('Examples of selection input fields')
            ->fields([
                ConfigField::make('select_example')
                    ->label('Select')
                    ->select([
                        'option1' => 'Option One',
                        'option2' => 'Option Two',
                        'option3' => 'Option Three',
                    ])
                    ->default('option1')
                    ->helperText('Single selection dropdown.')
                    ->sort(10),

                ConfigField::make('multiselect_example')
                    ->label('Multi-Select')
                    ->multiSelect([
                        'red' => 'Red',
                        'green' => 'Green',
                        'blue' => 'Blue',
                        'yellow' => 'Yellow',
                    ])
                    ->default(['red', 'blue'])
                    ->helperText('Multiple selection dropdown.')
                    ->sort(20),

                ConfigField::make('color_example')
                    ->label('Color Picker')
                    ->color()
                    ->default('#3b82f6')
                    ->helperText('Color picker input.')
                    ->sort(30),

                ConfigField::make('color_palette_example')
                    ->label('Color Palette')
                    ->colorPalette([
                        'red' => 'Red',
                        'green' => 'Green',
                        'blue' => 'Blue',
                        'purple' => 'Purple',
                    ])
                    ->default('blue')
                    ->helperText('Select from predefined colors.')
                    ->sort(40),
            ]);
    }

    protected static function toggleFieldsGroup(): ConfigGroup
    {
        return ConfigGroup::make('toggle_fields')
            ->label('Toggle Fields')
            ->icon('heroicon-o-adjustments-horizontal')
            ->description('Examples of boolean toggle fields')
            ->fields([
                ConfigField::make('toggle_enabled')
                    ->label('Enable Feature')
                    ->toggle()
                    ->default(true)
                    ->helperText('Toggle to enable or disable.')
                    ->usedInCode()
                    ->sort(10),

                ConfigField::make('toggle_notifications')
                    ->label('Send Notifications')
                    ->toggle()
                    ->default(false)
                    ->helperText('Enable email notifications.')
                    ->sort(20),
            ]);
    }

    protected static function advancedFieldsGroup(): ConfigGroup
    {
        return ConfigGroup::make('advanced_fields')
            ->label('Advanced Fields')
            ->icon('heroicon-o-cog')
            ->description('Examples of advanced input fields')
            ->collapsed()
            ->fields([
                ConfigField::make('rich_editor_example')
                    ->label('Rich Editor')
                    ->richEditor()
                    ->helperText('WYSIWYG rich text editor.')
                    ->sort(10),

                ConfigField::make('image_example')
                    ->label('Image Upload')
                    ->image()
                    ->helperText('Upload an image file.')
                    ->sort(20),

                ConfigField::make('info_example')
                    ->label('Info Field')
                    ->info('app.name')
                    ->helperText('Displays a read-only value from Laravel config.')
                    ->systemValueOption(false)
                    ->sort(30),
            ]);
    }
}
