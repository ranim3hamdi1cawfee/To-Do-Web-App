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

use Symfony\Bundle\MakerBundle\Maker\MakeTwigComponent;
use Symfony\Bundle\MakerBundle\Test\MakerTestCase;
use Symfony\Bundle\MakerBundle\Test\MakerTestRunner;

class MakeTwigComponentTest extends MakerTestCase
{
    public static function getTestDetails(): \Generator
    {
        yield 'it_generates_twig_component' => [self::buildMakerTest()
            ->addExtraDependencies('symfony/ux-twig-component', 'symfony/twig-bundle')
            ->run(static function (MakerTestRunner $runner) {
                $output = $runner->runMaker(['Alert']);

                self::assertStringContainsString('src/Twig/Components/Alert.php', $output);
                self::assertStringContainsString('templates/components/Alert.html.twig', $output);
                self::assertStringContainsString('To render the component, use <twig:Alert />.', $output);

                $runner->copy(
                    'make-twig-component/tests/it_generates_twig_component.php',
                    'tests/GeneratedTwigComponentTest.php'
                );
                $runner->replaceInFile('tests/GeneratedTwigComponentTest.php', '{name}', 'Alert');
                $runner->runTests();
            }),
        ];

        yield 'it_generates_twig_component_in_non_default_namespace' => [self::buildMakerTest()
            ->addExtraDependencies('symfony/ux-twig-component', 'symfony/twig-bundle')
            ->run(static function (MakerTestRunner $runner) {
                $runner->copy(
                    'make-twig-component/custom_twig_component.yaml',
                    'config/packages/twig_component.yaml'
                );

                $output = $runner->runMaker(['Alert']);

                self::assertStringContainsString('src/Site/Twig/Components/Alert.php', $output);
                self::assertStringContainsString('templates/components/Alert.html.twig', $output);
                self::assertStringContainsString('To render the component, use <twig:Alert />.', $output);

                $runner->copy(
                    'make-twig-component/tests/it_generates_twig_component.php',
                    'tests/GeneratedTwigComponentTest.php'
                );
                $runner->replaceInFile('tests/GeneratedTwigComponentTest.php', '{name}', 'Alert');
                $runner->runTests();
            }),
        ];

        yield 'it_generates_pascal_case_twig_component' => [self::buildMakerTest()
            ->addExtraDependencies('symfony/ux-twig-component', 'symfony/twig-bundle')
            ->run(static function (MakerTestRunner $runner) {
                $output = $runner->runMaker(['FormInput']);

                self::assertStringContainsString('src/Twig/Components/FormInput.php', $output);
                self::assertStringContainsString('templates/components/FormInput.html.twig', $output);
                self::assertStringContainsString('To render the component, use <twig:FormInput />.', $output);

                $runner->copy(
                    'make-twig-component/tests/it_generates_twig_component.php',
                    'tests/GeneratedTwigComponentTest.php'
                );
                $runner->replaceInFile('tests/GeneratedTwigComponentTest.php', '{name}', 'FormInput');
                $runner->runTests();
            }),
        ];

        yield 'it_generates_live_component' => [self::buildMakerTest()
            ->addExtraDependencies('symfony/ux-live-component', 'symfony/twig-bundle')
            ->run(static function (MakerTestRunner $runner) {
                $output = $runner->runMaker(['Alert', 'y']);

                self::assertStringContainsString('src/Twig/Components/Alert.php', $output);
                self::assertStringContainsString('templates/components/Alert.html.twig', $output);
                self::assertStringContainsString('To render the component, use <twig:Alert />.', $output);

                $runner->copy(
                    'make-twig-component/tests/it_generates_live_component.php',
                    'tests/GeneratedLiveComponentTest.php'
                );
                $runner->replaceInFile('tests/GeneratedLiveComponentTest.php', '{name}', 'Alert');
                $runner->runTests();
            }),
        ];

        yield 'it_generates_pascal_case_live_component' => [self::buildMakerTest()
            ->addExtraDependencies('symfony/ux-live-component', 'symfony/twig-bundle')
            ->run(static function (MakerTestRunner $runner) {
                $output = $runner->runMaker(['FormInput', 'y']);

                self::assertStringContainsString('src/Twig/Components/FormInput.php', $output);
                self::assertStringContainsString('templates/components/FormInput.html.twig', $output);
                self::assertStringContainsString('To render the component, use <twig:FormInput />.', $output);

                $runner->copy(
                    'make-twig-component/tests/it_generates_live_component.php',
                    'tests/GeneratedLiveComponentTest.php'
                );
                $runner->replaceInFile('tests/GeneratedLiveComponentTest.php', '{name}', 'FormInput');
                $runner->runTests();
            }),
        ];

        yield 'it_generates_live_component_on_subdirectory' => [self::buildMakerTest()
            ->addExtraDependencies('symfony/ux-live-component', 'symfony/twig-bundle')
            ->run(static function (MakerTestRunner $runner) {
                $output = $runner->runMaker(['Form\Input', 'y']);

                self::assertStringContainsString('src/Twig/Components/Form/Input.php', $output);
                self::assertStringContainsString('templates/components/Form/Input.html.twig', $output);
                self::assertStringContainsString('To render the component, use <twig:Form:Input />.', $output);

                $runner->copy(
                    'make-twig-component/tests/it_generates_live_component.php',
                    'tests/GeneratedLiveComponentTest.php'
                );
                $runner->replaceInFile('tests/GeneratedLiveComponentTest.php', '{name}', 'Form:Input');
                $runner->runTests();
            }),
        ];
    }

    protected function getMakerClass(): string
    {
        return MakeTwigComponent::class;
    }
}
