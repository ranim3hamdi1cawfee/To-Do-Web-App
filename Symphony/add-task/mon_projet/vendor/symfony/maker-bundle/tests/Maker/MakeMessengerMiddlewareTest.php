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

use Symfony\Bundle\MakerBundle\Maker\MakeMessengerMiddleware;
use Symfony\Bundle\MakerBundle\Test\MakerTestCase;
use Symfony\Bundle\MakerBundle\Test\MakerTestRunner;

class MakeMessengerMiddlewareTest extends MakerTestCase
{
    protected function getMakerClass(): string
    {
        return MakeMessengerMiddleware::class;
    }

    public static function getTestDetails(): \Generator
    {
        yield 'it_generates_messenger_middleware' => [self::buildMakerTest()
            ->run(static function (MakerTestRunner $runner) {
                $runner->runMaker(
                    [
                        // middleware name
                        'CustomMiddleware',
                    ]);

                self::assertFileExists($runner->getPath('src/Middleware/CustomMiddleware.php'));
            }),
        ];
    }
}
