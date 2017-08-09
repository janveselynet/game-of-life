<?php

namespace Life\IO;

use Life\Environment\Cell;
use Life\Exceptions\InvalidInputException;
use Life\TestCase;
use Life\Utils\Random;
use Mockery\MockInterface;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

class XmlFileReaderTest extends TestCase
{

    public function testReadingValidFile()
    {
        $path = __DIR__ . '/files/input-valid.xml';
        $random = $this->createRandomMockThatIsNotUsed();
        $reader = new XmlFileReader($path, $random);
        Assert::same(100, $reader->getIterationsCount());
        $world = $reader->getInitialWorld();
        $expectedSize = 5;
        Assert::same($expectedSize, $world->getSize());
        Assert::same(3, $world->getSpecies());
        $cells = $world->getCells();
        $expectedSpecies = [
            [1,    null, 2,    null, 1   ],
            [null, null, null, null, null],
            [2,    null, 1,    null, 2   ],
            [null, null, null, null, null],
            [1,    null, 2,    null, 1   ],
        ];
        for ($y = 0; $y < $expectedSize; $y++) {
            for ($x = 0; $x < $expectedSize; $x++) {
                $cell = $cells[$y][$x]; /** @var Cell $cell */
                Assert::same($expectedSpecies[$y][$x], $cell->getOrganism());
            }
        }
    }

    /**
     * @param string $fileName
     * @param string $exceptionMessage
     * @dataProvider getInvalidFiles
     */
    public function testReadingThrowsExceptionWhenGivenInvalidFile(string $fileName, string $exceptionMessage)
    {
        Assert::exception(
            function() use($fileName) {
                $path = __DIR__ . '/files/' . $fileName;
                $random = $this->createRandomMockThatIsNotUsed();
                $reader = new XmlFileReader($path, $random);
                $reader->getInitialWorld();
            },
            'Life\\Exceptions\\InvalidInputException',
            $exceptionMessage
        );
    }

    /**
     * @return array
     */
    public function getInvalidFiles()
    {
        return [
            ["input-nonexistent.xml", "Unable to read nonexistent file"],
            ["input-empty.xml", "Cannot read XML file"],
            ["input-invalid-xml.xml", "Cannot read XML file"],
            ["input-missing-world.xml", "Missing element 'world'"],
            ["input-missing-iterations.xml", "Missing element 'iterations'"],
            ["input-missing-cells.xml", "Missing element 'cells'"],
            ["input-missing-species.xml", "Missing element 'species'"],
            ["input-missing-organisms.xml", "Missing element 'organisms'"],
            ["input-missing-organism-xpos.xml", "Missing element 'x_pos' in some of the element 'organism'"],
            ["input-missing-organism-ypos.xml", "Missing element 'y_pos' in some of the element 'organism'"],
            ["input-missing-organism-species.xml", "Missing element 'species' in some of the element 'organism'"],
            ["input-negative-iterations.xml", "Value of element 'iterations' must be zero or positive number"],
            ["input-negative-cells.xml", "Value of element 'cells' must be positive number"],
            ["input-zero-cells.xml", "Value of element 'cells' must be positive number"],
            ["input-negative-species.xml", "Value of element 'species' must be positive number"],
            ["input-zero-species.xml", "Value of element 'species' must be positive number"],
            ["input-negative-organism-xpos.xml", "Value of element 'x_pos' of element 'organism' must be between 0 and number of cells"],
            ["input-exceeded-organism-xpos.xml", "Value of element 'x_pos' of element 'organism' must be between 0 and number of cells"],
            ["input-negative-organism-ypos.xml", "Value of element 'y_pos' of element 'organism' must be between 0 and number of cells"],
            ["input-exceeded-organism-ypos.xml", "Value of element 'y_pos' of element 'organism' must be between 0 and number of cells"],
            ["input-negative-organism-species.xml", "Value of element 'species' of element 'organism' must be between 0 and maximal number of species"],
            ["input-exceeded-organism-species.xml", "Value of element 'species' of element 'organism' must be between 0 and maximal number of species"],
        ];
    }

    public function testChoosingSpeciesRandomlyIfMoreOrganismsAreInOnePosition()
    {
        $path = __DIR__ . '/files/input-duplicate.xml';
        $random = \Mockery::mock(Random::class); /** @var MockInterface|Random $random */
        $random->shouldReceive('getRandomArrayValue')->once()->with([0, 1])->andReturn(1);
        $reader = new XmlFileReader($path, $random);
        $world = $reader->getInitialWorld();
        $cell = $world->getCells()[0][0]; /** @var Cell $cell */
        Assert::same(1, $cell->getOrganism());
    }

    /**
     * @return MockInterface|Random
     */
    private function createRandomMockThatIsNotUsed()
    {
        $random = \Mockery::mock(Random::class);
        $random->shouldReceive('getRandomArrayValue')->never();
        return $random;
    }

}

(new XmlFileReaderTest())->run();
