<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigValue extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'path',
        'value',
    ];

    public function getTable(): string
    {
        return config('filament-system-configuration.table_name', 'config_values');
    }
}
