<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration\Config;

use Closure;

class ConfigGroup
{
    protected string $label;

    protected ?string $icon = null;

    protected ?string $description = null;

    protected int $sort = 0;

    protected bool $collapsible = true;

    protected bool $collapsed = false;

    /** @var array<ConfigField> */
    protected array $fields = [];

    protected ?Closure $isVisibleUsing = null;

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

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function sort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public function collapsible(bool $collapsible = true): static
    {
        $this->collapsible = $collapsible;

        return $this;
    }

    public function collapsed(bool $collapsed = true): static
    {
        $this->collapsed = $collapsed;

        return $this;
    }

    /**
     * @param  array<ConfigField>  $fields
     */
    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    public function visible(Closure $callback): static
    {
        $this->isVisibleUsing = $callback;

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

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function isCollapsible(): bool
    {
        return $this->collapsible;
    }

    public function isCollapsed(): bool
    {
        return $this->collapsed;
    }

    /**
     * @return array<ConfigField>
     */
    public function getFields(): array
    {
        $fields = array_filter($this->fields, fn (ConfigField $field): bool => $field->isVisible());

        usort($fields, fn (ConfigField $a, ConfigField $b): int => $a->getSort() <=> $b->getSort());

        return $fields;
    }

    public function isVisible(): bool
    {
        if ($this->isVisibleUsing instanceof Closure) {
            return (bool) call_user_func($this->isVisibleUsing);
        }

        return true;
    }

    /**
     * Get all field paths for this group.
     *
     * @return array<string>
     */
    public function getFieldPaths(string $sectionKey): array
    {
        $paths = [];

        foreach ($this->getFields() as $field) {
            $paths[] = "{$sectionKey}/{$this->key}/{$field->getKey()}";
        }

        return $paths;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'label' => $this->getLabel(),
            'icon' => $this->getIcon(),
            'description' => $this->getDescription(),
            'sort' => $this->getSort(),
            'collapsible' => $this->isCollapsible(),
            'collapsed' => $this->isCollapsed(),
            'fields' => array_map(fn (ConfigField $field): array => $field->toArray(), $this->getFields()),
        ];
    }
}
