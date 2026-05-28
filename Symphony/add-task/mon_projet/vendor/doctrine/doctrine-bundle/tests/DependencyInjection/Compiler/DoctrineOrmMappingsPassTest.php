<?php

declare(strict_types=1);

namespace Doctrine\Bundle\DoctrineBundle\Tests\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\DoctrineBundle\Tests\TestCase;
use Doctrine\Deprecations\PHPUnit\VerifyDeprecations;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;

class DoctrineOrmMappingsPassTest extends TestCase
{
    use VerifyDeprecations;

    #[IgnoreDeprecations]
    public function testCreateYamlMappingDriverIsDeprecated(): void
    {
        $this->expectDeprecationWithIdentifier('https://github.com/doctrine/DoctrineBundle/pull/2088');

        DoctrineOrmMappingsPass::createYamlMappingDriver(['/path/to/namespace' => 'App\\Entity']);
    }

    #[IgnoreDeprecations]
    public function testCreateAnnotationMappingDriverIsDeprecated(): void
    {
        $this->expectDeprecationWithIdentifier('https://github.com/doctrine/DoctrineBundle/pull/2088');

        DoctrineOrmMappingsPass::createAnnotationMappingDriver(
            ['App\\Entity'],
            ['/path/to/entities'],
        );
    }
}
