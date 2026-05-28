<?php

declare(strict_types=1);

namespace Doctrine\Bundle\DoctrineBundle\Tests\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\Tests\DependencyInjection\Fixtures\TestKernel;
use Doctrine\Bundle\DoctrineBundle\Tests\TestCase;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\Deprecations\PHPUnit\VerifyDeprecations;
use Doctrine\ORM\Cache\CacheEntry;
use Doctrine\ORM\Cache\CacheKey;
use Doctrine\ORM\Cache\CollectionCacheEntry;
use Doctrine\ORM\Cache\Lock;
use Doctrine\ORM\Cache\Region;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

use function interface_exists;
use function restore_exception_handler;

class CacheCompatibilityPassTest extends TestCase
{
    use VerifyDeprecations;

    public static function setUpBeforeClass(): void
    {
        if (interface_exists(EntityManagerInterface::class)) {
            return;
        }

        self::markTestSkipped('This test requires ORM');
    }

    public function testCacheConfigUsingServiceDefinedByApplication(): void
    {
        (new class (false) extends TestKernel {
            public function registerContainerConfiguration(LoaderInterface $loader): void
            {
                parent::registerContainerConfiguration($loader);
                $loader->load(static function (ContainerBuilder $containerBuilder): void {
                    $containerBuilder->loadFromExtension('framework', [
                        'cache' => [
                            'pools' => [
                                'doctrine.system_cache_pool' => ['adapter' => 'cache.system'],
                            ],
                        ],
                    ]);
                    $containerBuilder->loadFromExtension(
                        'doctrine',
                        [
                            'orm' => [
                                'controller_resolver' => ['auto_mapping' => false],
                                'query_cache_driver' => ['type' => 'service', 'id' => 'custom_cache_service'],
                                'result_cache_driver' => ['type' => 'pool', 'pool' => 'doctrine.system_cache_pool'],
                                'second_level_cache' => [
                                    'enabled' => true,
                                    'regions' => [
                                        'filelock' => ['type' => 'filelock', 'lifetime' => 0, 'cache_driver' => ['type' => 'pool', 'pool' => 'doctrine.system_cache_pool']],
                                        'lifelong' => ['lifetime' => 0, 'cache_driver' => ['type' => 'pool', 'pool' => 'doctrine.system_cache_pool']],
                                        'entity_cache_region' => [
                                            'type' => 'service',
                                            'service' => TestRegion::class,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    );
                    $containerBuilder->register(TestRegion::class, TestRegion::class);
                    $containerBuilder->setDefinition(
                        'custom_cache_service',
                        new Definition(ArrayAdapter::class),
                    );
                });
            }
        })->boot();

        $this->addToAssertionCount(1);

        restore_exception_handler();
    }

    #[DoesNotPerformAssertions]
    public function testMetadataCacheConfigUsingPsr6ServiceDefinedByApplication(): void
    {
        (new class (false) extends TestKernel {
            public function registerContainerConfiguration(LoaderInterface $loader): void
            {
                parent::registerContainerConfiguration($loader);
                $loader->load(static function (ContainerBuilder $containerBuilder): void {
                    $containerBuilder->loadFromExtension(
                        'doctrine',
                        [
                            'orm' => [
                                'controller_resolver' => ['auto_mapping' => false],
                                'metadata_cache_driver' => ['type' => 'service', 'id' => 'custom_cache_service'],
                            ],
                        ],
                    );
                    $containerBuilder->setDefinition(
                        'custom_cache_service',
                        new Definition(ArrayAdapter::class),
                    );
                });
            }
        })->boot();

        restore_exception_handler();
    }

    #[IgnoreDeprecations]
    public function testMetadataCacheConfigUsingNonPsr6ServiceDefinedByApplication(): void
    {
        $this->expectDeprecationWithIdentifier('https://github.com/doctrine/DoctrineBundle/pull/1365');
        (new class (false) extends TestKernel {
            public function registerContainerConfiguration(LoaderInterface $loader): void
            {
                parent::registerContainerConfiguration($loader);
                $loader->load(static function (ContainerBuilder $containerBuilder): void {
                    $containerBuilder->loadFromExtension(
                        'doctrine',
                        ['orm' => ['metadata_cache_driver' => ['type' => 'service', 'id' => 'custom_cache_service']]],
                    );
                    $containerBuilder->setDefinition(
                        'custom_cache_service',
                        (new Definition(DoctrineProvider::class))
                            ->setArguments([new Definition(ArrayAdapter::class)])
                            ->setFactory([DoctrineProvider::class, 'wrap']),
                    );
                });
            }
        })->boot();

        restore_exception_handler();
    }
}

if (! interface_exists(Region::class)) {
    return;
}

final class TestRegion implements Region
{
    public function getName(): string
    {
        return 'test_region';
    }

    public function contains(CacheKey $key): bool
    {
        throw new RuntimeException('Not implemented');
    }

    public function get(CacheKey $key): CacheEntry|null
    {
        throw new RuntimeException('Not implemented');
    }

    public function getMultiple(CollectionCacheEntry $collection): array|null
    {
        throw new RuntimeException('Not implemented');
    }

    public function put(CacheKey $key, CacheEntry $entry, Lock|null $lock = null): bool
    {
        throw new RuntimeException('Not implemented');
    }

    public function evict(CacheKey $key): bool
    {
        throw new RuntimeException('Not implemented');
    }

    public function evictAll(): bool
    {
        throw new RuntimeException('Not implemented');
    }
}
