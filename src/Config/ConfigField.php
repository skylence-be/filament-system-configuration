<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration\Config;

use Closure;
use Skylence\FilamentSystemConfiguration\Enums\ConfigFieldType;

class ConfigField
{
    protected string $label;

    protected ConfigFieldType $type = ConfigFieldType::Text;

    protected mixed $default = null;

    protected ?string $helperText = null;

    protected ?string $placeholder = null;

    /** @var array<string, string>|Closure|null */
    protected array|Closure|null $options = null;

    /** @var array<string> */
    protected array $rules = [];

    protected bool $required = false;

    protected int $sort = 0;

    protected bool $encrypted = false;

    protected bool $canUseSystemValue = true;

    protected ?string $configKey = null;

    protected ?Closure $isVisibleUsing = null;

    protected bool $usedInCode = false;

    public static function make(string $key): static
    {
        return new static($key);
    }

    final public function __construct(protected string $key) {}

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function type(ConfigFieldType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function text(): static
    {
        return $this->type(ConfigFieldType::Text);
    }

    public function textarea(): static
    {
        return $this->type(ConfigFieldType::Textarea);
    }

    /**
     * @param  array<string, string>|Closure  $options
     */
    public function select(array|Closure $options = []): static
    {
        $this->options = $options;

        return $this->type(ConfigFieldType::Select);
    }

    /**
     * @param  array<string, string>|Closure  $options
     */
    public function multiSelect(array|Closure $options = []): static
    {
        $this->options = $options;

        return $this->type(ConfigFieldType::MultiSelect);
    }

    public function toggle(): static
    {
        return $this->type(ConfigFieldType::Toggle);
    }

    public function number(): static
    {
        return $this->type(ConfigFieldType::Number);
    }

    public function email(): static
    {
        return $this->type(ConfigFieldType::Email);
    }

    public function password(): static
    {
        return $this->type(ConfigFieldType::Password);
    }

    public function color(): static
    {
        return $this->type(ConfigFieldType::Color);
    }

    /**
     * @param  array<string, string>|Closure  $options
     */
    public function colorPalette(array|Closure $options = []): static
    {
        $this->options = $options;

        return $this->type(ConfigFieldType::ColorPalette);
    }

    public function image(): static
    {
        return $this->type(ConfigFieldType::Image);
    }

    public function richEditor(): static
    {
        return $this->type(ConfigFieldType::RichEditor);
    }

    public function info(?string $configKey = null): static
    {
        if ($configKey) {
            $this->configKey = $configKey;
        }

        return $this->type(ConfigFieldType::Info);
    }

    public function configKey(string $configKey): static
    {
        $this->configKey = $configKey;

        return $this;
    }

    public function default(mixed $value): static
    {
        $this->default = $value;

        return $this;
    }

    public function helperText(string $text): static
    {
        $this->helperText = $text;

        return $this;
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @param  array<string, string>|Closure  $options
     */
    public function options(array|Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param  array<string>  $rules
     */
    public function rules(array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function sort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function encrypted(bool $encrypted = true): static
    {
        $this->encrypted = $encrypted;

        return $this;
    }

    public function systemValueOption(bool $enabled = true): static
    {
        $this->canUseSystemValue = $enabled;

        return $this;
    }

    public function visible(Closure $callback): static
    {
        $this->isVisibleUsing = $callback;

        return $this;
    }

    public function usedInCode(bool $used = true): static
    {
        $this->usedInCode = $used;

        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLabel(): string
    {
        return $this->label ?? str($this->key)->replace('_', ' ')->title()->toString();
    }

    public function getType(): ConfigFieldType
    {
        return $this->type;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function getHelperText(): ?string
    {
        return $this->helperText;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    /**
     * @return array<string, string>
     */
    public function getOptions(): array
    {
        if ($this->options instanceof Closure) {
            return call_user_func($this->options);
        }

        return $this->options ?? [];
    }

    /**
     * @return array<string>
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function isEncrypted(): bool
    {
        return $this->encrypted;
    }

    public function hasSystemValueOption(): bool
    {
        return $this->canUseSystemValue;
    }

    public function getConfigKey(): ?string
    {
        return $this->configKey;
    }

    public function isVisible(): bool
    {
        if ($this->isVisibleUsing instanceof Closure) {
            return call_user_func($this->isVisibleUsing);
        }

        return true;
    }

    public function isUsedInCode(): bool
    {
        return $this->usedInCode;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'label' => $this->getLabel(),
            'type' => $this->getType()->value,
            'default' => $this->getDefault(),
            'helperText' => $this->getHelperText(),
            'placeholder' => $this->getPlaceholder(),
            'options' => $this->getOptions(),
            'rules' => $this->getRules(),
            'required' => $this->isRequired(),
            'sort' => $this->getSort(),
            'encrypted' => $this->isEncrypted(),
            'hasSystemValueOption' => $this->hasSystemValueOption(),
            'usedInCode' => $this->isUsedInCode(),
        ];
    }
}
