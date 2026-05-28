<?php

declare(strict_types=1);

namespace Fixtures\config\php;

use Doctrine\Bundle\DoctrineBundle\Tests\DeprecationFreeConfig;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('doctrine', DeprecationFreeConfig::get());
};
