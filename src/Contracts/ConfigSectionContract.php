<?php

declare(strict_types=1);

namespace Skylence\FilamentSystemConfiguration\Contracts;

use Skylence\FilamentSystemConfiguration\Config\ConfigSection;

interface ConfigSectionContract
{
    public static function make(): ConfigSection;
}
