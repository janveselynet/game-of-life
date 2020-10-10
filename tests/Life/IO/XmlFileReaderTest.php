<?php declare(strict_types = 1);

namespace Life\IO;

use Generator;
use Life\Environment\Cell;
use Life\Environment\World;
use Life\TestCase;
use Life\Random;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

final class XmlFileReaderTest extends TestCase
{
    public function testReadingValidFile(): void
    {
        $path = __DIR__ . '/files/input-valid.xml';
        $reader = new XmlFileReader($path, new Random());

        Assert::same(100, $reader->getIterationsCount());

        $expectedCellOrganisms = [
            [1,    null, 2,    null, 1   ],
            [null, null, null, null, null],
            [2,    null, 1,    null, 2   ],
            [null, null, null, null, null],
            [1,    null, 2,    null, 1   ],
        ];
        $this->assertWorld(5, 3, $expectedCellOrganisms, $reader->getInitialWorld());
    }

    /**
     * @dataProvider invalidFilesProvider
     *
     */
    public function testReadingThrowsExceptionWhenGivenFileIsInvalid(
        string $expectedExceptionMessage,
        string $fileName
    ): void {
        Assert::exception(
            static function () use ($fileName): void {
                $path = __DIR__ . '/files/' . $fileName;
                $reader = new XmlFileReader($path, new Random());
                $reader->getInitialWorld();
            },
            InvalidInputException::class,
            $expectedExceptionMessage
        );
    }


    /**
     * @return Generator<array<mixed>>
     */
    public function invalidFilesProvider(): Generator
    {
        yield 'non existent file' => [
            'expectedExceptionMessage' => 'Unable to read nonexistent file',
            'fileName' => 'input-nonexistent.xml',
        ];

        yield 'empty xml file' => [
            'expectedExceptionMessage' => 'Cannot read XML file',
            'fileName' => 'input-empty.xml',
        ];

        yield 'invalid xml file' => [
            'expectedExceptionMessage' => 'Cannot read XML file',
            'fileName' => 'input-invalid-xml.xml',
        ];

        yield 'missing element world' => [
            'expectedExceptionMessage' => "Missing element 'world'",
            'fileName' => 'input-missing-world.xml',
        ];

        yield 'missing element iterations' => [
            'expectedExceptionMessage' => "Missing element 'iterations'",
            'fileName' => 'input-missing-iterations.xml',
        ];

        yield 'missing elements cells' => [
            'expectedExceptionMessage' => "Missing element 'cells'",
            'fileName' => 'input-missing-cells.xml',
        ];

        yield 'missing element species' => [
            'expectedExceptionMessage' => "Missing element 'species'",
            'fileName' => 'input-missing-species.xml',
        ];

        yield 'missing element organisms' => [
            'expectedExceptionMessage' => "Missing element 'organisms'",
            'fileName' => 'input-missing-organisms.xml',
        ];

        yield 'missing organism x position' => [
            'expectedExceptionMessage' => "Missing element 'x_pos' in some of the element 'organism'",
            'fileName' => 'input-missing-organism-xpos.xml',
        ];

        yield 'missing organism y position' => [
            'expectedExceptionMessage' => "Missing element 'y_pos' in some of the element 'organism'",
            'fileName' => 'input-missing-organism-ypos.xml',
        ];

        yield 'missing organism species value' => [
            'expectedExceptionMessage' => "Missing element 'species' in some of the element 'organism'",
            'fileName' => 'input-missing-organism-species.xml',
        ];

        yield 'negative value in iterations' => [
            'expectedExceptionMessage' => "Value of element 'iterations' must be zero or positive number",
            'fileName' => 'input-negative-iterations.xml',
        ];

        yield 'negative number in cells' => [
            'expectedExceptionMessage' => "Value of element 'cells' must be positive number",
            'fileName' => 'input-negative-cells.xml',
        ];

        yield 'zero number of cells' => [
            'expectedExceptionMessage' => "Value of element 'cells' must be positive number",
            'fileName' => 'input-zero-cells.xml',
        ];

        yield 'negative number in species' => [
            'expectedExceptionMessage' => "Value of element 'species' must be positive number",
            'fileName' => 'input-negative-species.xml',
        ];

        yield 'zero number in species' => [
            'expectedExceptionMessage' => "Value of element 'species' must be positive number",
            'fileName' => 'input-zero-species.xml',
        ];

        yield 'negative value in x position of organism' => [
            'expectedExceptionMessage' => "Value of element 'x_pos' of element 'organism' must be "
                . 'between 0 and number of cells',
            'fileName' => 'input-negative-organism-xpos.xml',
        ];

        yield 'too big value in x position of organism' => [
            'expectedExceptionMessage' => "Value of element 'x_pos' of element 'organism' must be "
                . 'between 0 and number of cells',
            'fileName' => 'input-exceeded-organism-xpos.xml',
        ];

        yield 'negative value in y position of organism' => [
            'expectedExceptionMessage' => "Value of element 'y_pos' of element 'organism' must be "
                . 'between 0 and number of cells',
            'fileName' => 'input-negative-organism-ypos.xml',
        ];

        yield 'too big value in y position of organism' => [
            'expectedExceptionMessage' => "Value of element 'y_pos' of element 'organism' must be "
                . 'between 0 and number of cells',
            'fileName' => 'input-exceeded-organism-ypos.xml',
        ];

        yield 'negative species in organism' => [
            'expectedExceptionMessage' => "Value of element 'species' of element 'organism' must be "
                . 'between 0 and maximal number of species',
            'fileName' => 'input-negative-organism-species.xml',
        ];

        yield 'zero species in organism' => [
            'expectedExceptionMessage' => "Value of element 'species' of element 'organism' must be "
                . 'between 0 and maximal number of species',
            'fileName' => 'input-exceeded-organism-species.xml',
        ];
    }

    public function testChoosingSpeciesRandomlyIfMoreOrganismsAreInOnePosition(): void
    {
        $path = __DIR__ . '/files/input-duplicate.xml';
        $reader = new XmlFileReader($path, $this->createRandomMock());

        $world = $reader->getInitialWorld();

        /** @var Cell $cell */
        $cell = $world->getCells()[0][0];
        Assert::same(1, $cell->getOrganismSpecies());
    }

    /**
     * @return Random&MockInterface
     */
    private function createRandomMock(): Random
    {
        $random = Mockery::mock(Random::class);
        $random->shouldReceive('getRandomArrayValue')
            ->once()
            ->with([0, 1])
            ->andReturn(1);

        return $random;
    }

    /**
     * @param array<array<int|null>> $expectedCellOrganisms
     */
    private function assertWorld(
        int $expectedSize,
        int $expectedSpeciesCount,
        array $expectedCellOrganisms,
        World $world
    ): void {
        Assert::same($expectedSize, $world->getSize());
        Assert::same($expectedSpeciesCount, $world->getSpeciesCount());

        $cells = $world->getCells();
        for ($y = 0; $y < $expectedSize; $y++) {
            for ($x = 0; $x < $expectedSize; $x++) {
                /** @var Cell $cell */
                $cell = $cells[$y][$x];
                Assert::same($expectedCellOrganisms[$y][$x], $cell->getOrganismSpecies());
            }
        }
    }
}

(new XmlFileReaderTest())->run();
