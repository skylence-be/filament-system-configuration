<?php

declare(strict_types=1);

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'print_r'])
    ->each->not->toBeUsed();

arch('strict types are enforced')
    ->expect('Skylence\FilamentSystemConfiguration')
    ->toUseStrictTypes();

arch('contracts are interfaces')
    ->expect('Skylence\FilamentSystemConfiguration\Contracts')
    ->toBeInterfaces();

arch('enums are enums')
    ->expect('Skylence\FilamentSystemConfiguration\Enums')
    ->toBeEnums();

arch('concerns are traits')
    ->expect('Skylence\FilamentSystemConfiguration\Concerns')
    ->toBeTraits();
