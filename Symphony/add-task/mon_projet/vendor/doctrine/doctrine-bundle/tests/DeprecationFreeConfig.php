<?php

declare(strict_types=1);

namespace Doctrine\Bundle\DoctrineBundle\Tests;

use Doctrine\ORM\Configuration;

use function method_exists;

use const PHP_VERSION_ID;

final class DeprecationFreeConfig
{
    /** @return array{'orm': array<string, mixed>} */
    public static function get(): array
    {
        return [
            'orm' => [
                'enable_lazy_ghost_objects' => true,
                'controller_resolver' => ['auto_mapping' => false],
                /** @phpstan-ignore function.alreadyNarrowedType */
                'enable_native_lazy_objects' => PHP_VERSION_ID >= 80400 && method_exists(
                    Configuration::class,
                    'enableNativeLazyObjects',
                ),
            ],
        ];
    }
}
