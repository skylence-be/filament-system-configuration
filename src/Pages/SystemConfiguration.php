<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;
use Skylence\FilamentContextSidebar\Concerns\HasContextSidebar;
use Skylence\FilamentSystemConfiguration\Concerns\HasConfigurationSidebar;
use Skylence\FilamentSystemConfiguration\Config\ConfigField;
use Skylence\FilamentSystemConfiguration\Config\ConfigSection;
use Skylence\FilamentSystemConfiguration\Enums\ConfigFieldType;
use Skylence\FilamentSystemConfiguration\Services\ConfigService;
use Skylence\FilamentSystemConfiguration\SystemConfig;

class SystemConfiguration extends Page implements HasForms
{
    use HasConfigurationSidebar;
    use HasContextSidebar;
    use InteractsWithForms;

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Cog6Tooth;

    public static function canAccess(): bool
    {
        $permission = config('filament-system-configuration.permissions.view', 'view_system_configuration');

        if ($permission === false) {
            return true;
        }

        return auth()->user()?->can($permission) ?? false;
    }

    protected static ?string $navigationLabel = 'Configuration';

    protected static ?string $title = 'System Configuration';

    protected static ?string $slug = 'system/configuration';

    protected static ?int $navigationSort = 100;

    protected static bool $shouldRegisterNavigation = true;

    protected string $view = 'filament-system-configuration::system-configuration';

    protected Width|string|null $maxContentWidth = Width::Full;

    #[Url(as: 'section')]
    public string $activeSection = 'general';

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void
    {
        $this->activeSection = $this->getDefaultSection();
        $this->loadFormData();
    }

    protected function getDefaultSection(): string
    {
        $requested = request()->query('section');

        if ($requested && SystemConfig::hasSection($requested)) {
            return $requested;
        }

        // Return first available section
        $sections = SystemConfig::getSections();

        return ! empty($sections) ? $sections[0]->getKey() : 'general';
    }

    public function getTitle(): string|Htmlable
    {
        $section = $this->getCurrentSection();

        return $section?->getLabel() ?? config('filament-system-configuration.page.title', 'System Configuration');
    }

    public function getSubheading(): ?string
    {
        $section = $this->getCurrentSection();
        $groupCount = $section instanceof ConfigSection ? count($section->getGroups()) : 0;

        return $groupCount > 0 ? "{$groupCount} configuration groups" : null;
    }

    public static function getNavigationLabel(): string
    {
        return config('filament-system-configuration.page.navigation_label', 'Configuration');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-system-configuration.page.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-system-configuration.page.navigation_sort', 100);
    }

    protected function getCurrentSection(): ?ConfigSection
    {
        return SystemConfig::getSection($this->activeSection);
    }

    protected function loadFormData(): void
    {
        $section = $this->getCurrentSection();

        if (! $section instanceof ConfigSection) {
            return;
        }

        $configService = app(ConfigService::class);
        $data = [];

        foreach ($section->getGroups() as $group) {
            foreach ($group->getFields() as $field) {
                $path = "{$section->getKey()}/{$group->getKey()}/{$field->getKey()}";
                $fieldName = $this->getFormFieldName($group->getKey(), $field->getKey());

                // Check if a custom value exists in the database
                $hasCustomValue = $configService->has($path);

                // Load the value (will return default if no custom value)
                $value = $configService->get($path, $field->getDefault());
                $data[$fieldName] = $value;

                // Set "use system value" checkbox (checked = use default, not custom)
                if ($field->hasSystemValueOption()) {
                    $data[$fieldName.'__use_system'] = ! $hasCustomValue;
                }
            }
        }

        $this->data = $data;
        $this->form->fill($data);
    }

    public function updatedActiveSection(): void
    {
        $this->loadFormData();
    }

    public function form(Schema $schema): Schema
    {
        $section = $this->getCurrentSection();

        if (! $section instanceof ConfigSection) {
            return $schema->components([]);
        }

        $components = [];

        foreach ($section->getGroups() as $group) {
            $fields = [];
            $groupFields = $group->getFields();
            $fieldCount = count($groupFields);

            foreach ($groupFields as $index => $field) {
                $fields[] = $this->buildFormField($group->getKey(), $field);

                // Add divider between fields (not after the last one)
                if ($index < $fieldCount - 1) {
                    $fields[] = View::make('filament-system-configuration::components.divider');
                }
            }

            $components[] = Section::make($group->getLabel())
                ->id($group->getKey())
                ->description($group->getDescription())
                ->icon($group->getIcon())
                ->collapsible($group->isCollapsible())
                ->collapsed($group->isCollapsed())
                ->schema($fields);
        }

        return $schema
            ->statePath('data')
            ->components($components);
    }

    protected function buildFormField(string $groupKey, ConfigField $field): mixed
    {
        $name = $this->getFormFieldName($groupKey, $field->getKey());
        $useSystemName = $name.'__use_system';

        $formField = match ($field->getType()) {
            ConfigFieldType::Text => TextInput::make($name)
                ->placeholder($field->getPlaceholder()),

            ConfigFieldType::Textarea => Textarea::make($name)
                ->placeholder($field->getPlaceholder())
                ->rows(3),

            ConfigFieldType::Email => TextInput::make($name)
                ->email()
                ->placeholder($field->getPlaceholder()),

            ConfigFieldType::Password => TextInput::make($name)
                ->password()
                ->revealable()
                ->placeholder($field->getPlaceholder()),

            ConfigFieldType::Number => TextInput::make($name)
                ->numeric()
                ->placeholder($field->getPlaceholder()),

            ConfigFieldType::Select => Select::make($name)
                ->options($field->getOptions())
                ->searchable(count($field->getOptions()) > 10)
                ->placeholder('Select an option'),

            ConfigFieldType::MultiSelect => Select::make($name)
                ->multiple()
                ->options($field->getOptions())
                ->searchable(count($field->getOptions()) > 10)
                ->placeholder('Select options'),

            ConfigFieldType::Toggle => Toggle::make($name)
                ->inline(false),

            ConfigFieldType::Color => ColorPicker::make($name),

            ConfigFieldType::ColorPalette => Radio::make($name)
                ->options(collect($field->getOptions())->mapWithKeys(fn ($label, string $value): array => [
                    $value => new HtmlString(
                        '<span class="inline-flex items-center gap-2">'.
                        '<span class="w-5 h-5 rounded border border-gray-300 dark:border-gray-600 shrink-0" style="background-color: '.$this->getTailwindColorHex($value).';"></span>'.
                        '<span class="text-xs">'.e($label).'</span>'.
                        '</span>'
                    ),
                ])->all())
                ->columns(['default' => 3, 'sm' => 4, 'md' => 5, 'lg' => 6]),

            ConfigFieldType::Image => FileUpload::make($name)
                ->image()
                ->disk(config('filament-system-configuration.uploads.disk', 'public'))
                ->directory(config('filament-system-configuration.uploads.directory', 'config'))
                ->visibility(config('filament-system-configuration.uploads.visibility', 'public')),

            ConfigFieldType::RichEditor => RichEditor::make($name)
                ->toolbarButtons([
                    'bold',
                    'italic',
                    'underline',
                    'strike',
                    'link',
                    'bulletList',
                    'orderedList',
                ]),

            ConfigFieldType::Info => TextInput::make($name)
                ->disabled()
                ->dehydrated(false)
                ->placeholder($field->getConfigKey() ? config($field->getConfigKey(), 'Not set') : ($field->getDefault() ?? 'Not set')),
        };

        // Build label with "Used in code" indicator
        $label = $field->getLabel();
        if ($field->isUsedInCode()) {
            $label = new HtmlString(
                e($field->getLabel()).'<br><span class="text-xs text-success-600 dark:text-success-400">Used in code</span>'
            );
        }

        $formField = $formField
            ->label($label)
            ->inlineLabel()
            ->helperText($field->getHelperText())
            ->required(fn (callable $get): bool => $field->isRequired() && ! $get($useSystemName))
            ->default($field->getDefault())
            ->rules($field->getRules());

        // Add "Use system value" checkbox if enabled for this field (not for Info fields)
        if ($field->hasSystemValueOption() && ! $field->getType()->isReadOnly()) {
            $formField = $formField
                ->disabled(fn (callable $get): bool => (bool) $get($useSystemName))
                ->dehydrated();

            return Grid::make(12)
                ->extraAttributes(['class' => 'items-start'])
                ->schema([
                    $formField->columnSpan(10),
                    Checkbox::make($useSystemName)
                        ->label('Use system value')
                        ->live()
                        ->default(true)
                        ->columnSpan(2),
                ]);
        }

        return $formField;
    }

    protected function getFormFieldName(string $groupKey, string $fieldKey): string
    {
        return "{$groupKey}__{$fieldKey}";
    }

    /**
     * @return array{group: string, field: string}
     */
    protected function parseFormFieldName(string $name): array
    {
        $parts = explode('__', $name);

        return [
            'group' => $parts[0] ?? '',
            'field' => $parts[1] ?? '',
        ];
    }

    public function save(): void
    {
        $permission = config('filament-system-configuration.permissions.update', 'update_system_configuration');

        if ($permission !== false) {
            abort_unless(auth()->user()?->can($permission), 403);
        }

        $section = $this->getCurrentSection();

        if (! $section instanceof ConfigSection) {
            return;
        }

        $data = $this->form->getState();
        $configService = app(ConfigService::class);

        foreach ($data as $key => $value) {
            // Skip the "use system value" checkbox keys
            if (str_ends_with((string) $key, '__use_system')) {
                continue;
            }

            $parsed = $this->parseFormFieldName($key);
            $path = "{$section->getKey()}/{$parsed['group']}/{$parsed['field']}";
            $useSystemKey = $key.'__use_system';

            // Check if "Use system value" checkbox is checked
            if (isset($data[$useSystemKey]) && $data[$useSystemKey]) {
                // Delete the custom value to use system default
                $configService->delete($path);
            } else {
                // Save the custom value
                $configService->set($path, $value);
            }
        }

        // Clear and refresh the cache
        $configService->refresh();

        Notification::make()
            ->title(config('filament-system-configuration.notifications.save.title', 'Configuration saved'))
            ->body(config('filament-system-configuration.notifications.save.body', 'Your settings have been saved successfully.'))
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(config('filament-system-configuration.actions.save.label', 'Save Configuration'))
                ->icon(Heroicon::Check)
                ->action('save')
                ->visible(function (): bool {
                    $permission = config('filament-system-configuration.permissions.update', 'update_system_configuration');

                    if ($permission === false) {
                        return true;
                    }

                    return auth()->user()?->can($permission) ?? false;
                }),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'currentSection' => $this->getCurrentSection(),
        ];
    }

    /**
     * Get the hex color value for a Tailwind color name (500 shade).
     */
    protected function getTailwindColorHex(string $color): string
    {
        return match ($color) {
            'slate' => '#64748b',
            'gray' => '#6b7280',
            'zinc' => '#71717a',
            'neutral' => '#737373',
            'stone' => '#78716c',
            'red' => '#ef4444',
            'orange' => '#f97316',
            'amber' => '#f59e0b',
            'yellow' => '#eab308',
            'lime' => '#84cc16',
            'green' => '#22c55e',
            'emerald' => '#10b981',
            'teal' => '#14b8a6',
            'cyan' => '#06b6d4',
            'sky' => '#0ea5e9',
            'blue' => '#3b82f6',
            'indigo' => '#6366f1',
            'violet' => '#8b5cf6',
            'purple' => '#a855f7',
            'fuchsia' => '#d946ef',
            'pink' => '#ec4899',
            'rose' => '#f43f5e',
            default => '#6b7280',
        };
    }
}
