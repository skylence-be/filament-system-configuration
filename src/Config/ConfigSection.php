<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration\Config;

use Closure;

class ConfigSection
{
    protected string $label;

    protected ?string $icon = null;

    protected int $sort = 0;

    /** @var array<ConfigGroup> */
    protected array $groups = [];

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

    public function sort(int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @param  array<ConfigGroup>  $groups
     */
    public function groups(array $groups): static
    {
        $this->groups = $groups;

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

    public function getIcon(): string
    {
        return $this->icon ?? 'heroicon-o-cog-6-tooth';
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @return array<ConfigGroup>
     */
    public function getGroups(): array
    {
        $groups = array_filter($this->groups, fn (ConfigGroup $group): bool => $group->isVisible());

        usort($groups, fn (ConfigGroup $a, ConfigGroup $b): int => $a->getSort() <=> $b->getSort());

        return $groups;
    }

    /**
     * Get a specific group by key.
     */
    public function getGroup(string $key): ?ConfigGroup
    {
        foreach ($this->getGroups() as $group) {
            if ($group->getKey() === $key) {
                return $group;
            }
        }

        return null;
    }

    public function isVisible(): bool
    {
        if ($this->isVisibleUsing instanceof Closure) {
            return call_user_func($this->isVisibleUsing);
        }

        return true;
    }

    /**
     * Get all field paths for this section.
     *
     * @return array<string>
     */
    public function getFieldPaths(): array
    {
        $paths = [];

        foreach ($this->getGroups() as $group) {
            $paths = array_merge($paths, $group->getFieldPaths($this->key));
        }

        return $paths;
    }

    /**
     * Get a field by its path within this section.
     */
    public function getField(string $groupKey, string $fieldKey): ?ConfigField
    {
        $group = $this->getGroup($groupKey);

        if (! $group instanceof ConfigGroup) {
            return null;
        }

        foreach ($group->getFields() as $field) {
            if ($field->getKey() === $fieldKey) {
                return $field;
            }
        }

        return null;
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
            'sort' => $this->getSort(),
            'groups' => array_map(fn (ConfigGroup $group): array => $group->toArray(), $this->getGroups()),
        ];
    }
}
