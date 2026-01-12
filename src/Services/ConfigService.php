<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Skylence\FilamentSystemConfiguration\Models\ConfigValue;
use Skylence\FilamentSystemConfiguration\SystemConfig;

class ConfigService
{
    /**
     * In-memory cache for the current request.
     *
     * @var array<string, mixed>|null
     */
    protected ?array $loaded = null;

    /**
     * Get the cache key from config.
     */
    protected function getCacheKey(): string
    {
        /** @var string */
        return config('filament-system-configuration.cache.key', 'system_config:all');
    }

    /**
     * Get the cache TTL from config.
     */
    protected function getCacheTtl(): int
    {
        /** @var int */
        return config('filament-system-configuration.cache.ttl', 86400);
    }

    /**
     * Get the table name from config.
     */
    protected function getTableName(): string
    {
        /** @var string */
        return config('filament-system-configuration.table_name', 'config_values');
    }

    /**
     * Get a configuration value by path.
     *
     * Priority: Database value > Laravel config > Field default > Static default > Provided default
     */
    public function get(string $path, mixed $default = null): mixed
    {
        $all = $this->all();

        // First check database values
        if (array_key_exists($path, $all)) {
            return $all[$path];
        }

        // Check if field has a Laravel config key mapping
        $field = SystemConfig::getFieldByPath($path);

        if ($field?->getConfigKey()) {
            $configValue = config($field->getConfigKey());
            if ($configValue !== null) {
                return $configValue;
            }
        }

        // Check field definition default
        if ($field !== null) {
            $fieldDefault = $field->getDefault();
            if ($fieldDefault !== null) {
                return $fieldDefault;
            }
        }

        // Check static config defaults
        /** @var array<string, mixed> $staticDefaults */
        $staticDefaults = config('filament-system-configuration.defaults', []);
        if (array_key_exists($path, $staticDefaults)) {
            return $staticDefaults[$path];
        }

        return $default;
    }

    /**
     * Get a value directly from Laravel config files.
     */
    public function getFromLaravelConfig(string $key, mixed $default = null): mixed
    {
        return config($key, $default);
    }

    /**
     * Get all configuration values (cached).
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        // Return in-memory cache if already loaded this request
        if ($this->loaded !== null) {
            return $this->loaded;
        }

        /** @var array<string, mixed> $cached */
        $cached = Cache::remember($this->getCacheKey(), $this->getCacheTtl(), $this->loadFromDatabase(...));
        $this->loaded = $cached;

        return $this->loaded;
    }

    /**
     * Load all configuration values from the database.
     *
     * @return array<string, mixed>
     */
    protected function loadFromDatabase(): array
    {
        // Handle case where table doesn't exist yet (fresh install, testing)
        try {
            if (! Schema::hasTable($this->getTableName())) {
                return [];
            }
        } catch (QueryException) {
            return [];
        }

        $values = [];

        $configValues = ConfigValue::query()->get();

        foreach ($configValues as $configValue) {
            $path = $configValue->path;
            $value = $configValue->value;
            $field = SystemConfig::getFieldByPath($path);

            // Decrypt if the field is marked as encrypted
            if ($field?->isEncrypted() && $value !== null) {
                try {
                    $value = Crypt::decryptString($value);
                } catch (\Exception) {
                    // If decryption fails, keep the raw value
                }
            }

            // Decode JSON for array fields (multiselect)
            if ($field?->getType()->isArrayType() && is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $decoded;
                }
            }

            $values[$path] = $value;
        }

        return $values;
    }

    /**
     * Set a configuration value.
     */
    public function set(string $path, mixed $value): void
    {
        $field = SystemConfig::getFieldByPath($path);

        // Encode arrays as JSON (for multiselect fields)
        if (is_array($value)) {
            $value = json_encode($value);
        }

        // Encrypt if the field is marked as encrypted
        if ($field?->isEncrypted() && $value !== null && $value !== '' && (is_string($value) || is_numeric($value))) {
            $value = Crypt::encryptString((string) $value);
        }

        ConfigValue::query()->updateOrCreate(
            ['path' => $path],
            ['value' => $value]
        );

        // Don't clear cache here - call clearCache() after batch operations
    }

    /**
     * Get multiple configuration values.
     *
     * @param  array<string>  $paths
     * @return array<string, mixed>
     */
    public function getMany(array $paths): array
    {
        $result = [];

        foreach ($paths as $path) {
            $result[$path] = $this->get($path);
        }

        return $result;
    }

    /**
     * Set multiple configuration values.
     *
     * @param  array<string, mixed>  $values
     */
    public function setMany(array $values): void
    {
        foreach ($values as $path => $value) {
            $this->set($path, $value);
        }

        $this->clearCache();
    }

    /**
     * Delete a configuration value.
     */
    public function delete(string $path): void
    {
        ConfigValue::query()
            ->where('path', $path)
            ->delete();

        // Don't clear cache here - call clearCache() after batch operations
    }

    /**
     * Delete multiple configuration values.
     *
     * @param  array<string>  $paths
     */
    public function deleteMany(array $paths): void
    {
        if ($paths === []) {
            return;
        }

        ConfigValue::query()
            ->whereIn('path', $paths)
            ->delete();

        $this->clearCache();
    }

    /**
     * Clear all configuration cache and reload.
     */
    public function clearCache(): void
    {
        Cache::forget($this->getCacheKey());
        $this->loaded = null;
    }

    /**
     * Refresh the cache (clear and reload).
     */
    public function refresh(): void
    {
        $this->clearCache();
        $this->all(); // Reload into cache
    }

    /**
     * Check if a configuration value exists in the database.
     */
    public function has(string $path): bool
    {
        return ConfigValue::query()
            ->where('path', $path)
            ->exists();
    }
}
