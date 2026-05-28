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
use Symfony\Bundle\MakerBundle\Maker\MakeFunctionalTest;
use Symfony\Bundle\MakerBundle\Test\MakerTestCase;
use Symfony\Bundle\MakerBundle\Test\MakerTestDetails;
use Symfony\Bundle\MakerBundle\Test\MakerTestRunner;

/**
 * @group legacy
 */
#[Group('legacy')]
class MakeFunctionalTestTest extends MakerTestCase
{
    protected function getMakerClass(): string
    {
        return MakeFunctionalTest::class;
    }

    public static function getTestDetails(): \Generator
    {
        yield 'it_generates_test_with_panther' => [self::getPantherTest()
            ->addExtraDependencies('panther')
            ->run(static function (MakerTestRunner $runner) {
                $runner->copy(
                    'make-functional/MainController.php',
                    'src/Controller/MainController.php'
                );
                $runner->copy(
                    'make-functional/routes.yaml',
                    'config/routes.yaml'
                );

                $runner->runMaker([
                    // functional test class name
                    'FooBar',
                ]);

                $runner->runTests();
            }),
        ];
    }

    protected static function getPantherTest(): MakerTestDetails
    {
        return self::buildMakerTest()
            ->skipTest(
                message: 'Panther test skipped - MAKER_SKIP_PANTHER_TEST set to TRUE.',
                skipped: getenv('MAKER_SKIP_PANTHER_TEST')
            );
    }
}
