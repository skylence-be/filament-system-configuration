<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $path
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ConfigValue extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'path',
        'value',
    ];

    public function getTable(): string
    {
        /** @var string */
        return config('filament-system-configuration.table_name', 'config_values');
    }
}
