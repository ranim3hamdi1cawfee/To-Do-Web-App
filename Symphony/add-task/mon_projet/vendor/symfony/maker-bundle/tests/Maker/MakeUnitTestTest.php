<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\MakerBundle\Tests\Maker;

use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\MakerBundle\Maker\MakeUnitTest;
use Symfony\Bundle\MakerBundle\Test\MakerTestCase;
use Symfony\Bundle\MakerBundle\Test\MakerTestRunner;

/**
 * @group legacy
 */
#[Group('legacy')]
class MakeUnitTestTest extends MakerTestCase
{
    protected function getMakerClass(): string
    {
        return MakeUnitTest::class;
    }

    public static function getTestDetails(): \Generator
    {
        yield 'it_makes_unit_test' => [self::buildMakerTest()
            ->run(static function (MakerTestRunner $runner) {
                $runner->runMaker(
                    [
                        // class name
                        'FooBar',
                    ]
                );

                $runner->runTests();
            }),
        ];
    }
}
