<?php


namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeneratedCrudControllerTest extends WebTestCase
{
    /**
     * Refers to the source folder where all generated files will be placed.
     * Facilitates access to src/, templates/ and tests/ folders.
     * @var string
     */
    private string $rootFolder = __DIR__ . "/..";

    public function testGeneratedTestsHasCorrectGettersAndSetters()
    {
        $testFileContent = file_get_contents($this->rootFolder . '/tests/Controller/FooControllerTest.php');

        $this->assertStringContainsString("getFooBar()", $testFileContent);
        $this->assertMatchesRegularExpression("/setFooBar(.*)/", $testFileContent);
        $this->assertStringNotContainsString("getFoo_bar()", $testFileContent);
    }
}
