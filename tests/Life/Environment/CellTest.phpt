<?php declare(strict_types=1);

namespace Life\Environment;

use Generator;
use Life\TestCase;
use Life\Random;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

final class CellTest extends TestCase
{
    /**
     * @dataProvider hasOrganismInCellProvider
     * @param bool $expectedResult
     * @param int|null $organism
     */
    public function testGettingThatSomeOrganismIsOnCell(bool $expectedResult, ?int $organism): void
    {
        $cell = new Cell($organism);

        $actualResult = $cell->hasOrganism();

        Assert::same($expectedResult, $actualResult);
    }

    public function hasOrganismInCellProvider(): Generator
    {
        yield 'no organism' => [
            'expectedResult' => false,
            'organism' => null,
        ];

        yield 'has organism' => [
            'expectedResult' => true,
            'organism' => 10,
        ];
    }

    /**
     * @dataProvider organismToEvolveProvider
     * @param int|null $expectedOrganismInCell
     * @param int|null $originalOrganismInCell
     * @param int[] $neighbours
     * @param Random $random
     */
    public function testCellIsEvolved(
        ?int $expectedOrganismInCell,
        ?int $originalOrganismInCell,
        array $neighbours,
        Random $random
    ): void {
        $neighbourCells = $this->getNeighboursFromNeighbourValues($neighbours);
        $originalCell = new Cell($originalOrganismInCell);

        $cellAfterEvolution = $originalCell->evolve($neighbourCells, $random);

        Assert::same($expectedOrganismInCell, $cellAfterEvolution->getOrganismSpecies());
    }

    public function organismToEvolveProvider(): Generator
    {
        yield 'organism 1 will survive with right number of neighbours' => [
            'expectedOrganismInCell' => 1,
            'originalOrganismInCell' => 1,
            'neighbours' => [1, 2, 1, 1, 4, 2],
            'random' => new Random(),
        ];

        yield 'organism 2 will survive with right number of neighbours' => [
            'expectedOrganismInCell' => 2,
            'originalOrganismInCell' => 2,
            'neighbours' => [1, 2, 1, 1, 4, 2],
            'random' => new Random(),
        ];

        yield 'new organism is born when current cannot survive' => [
            'expectedOrganismInCell' => 1,
            'originalOrganismInCell' => 4,
            'neighbours' => [1, 2, 1, 1, 4, 2],
            'random' => new Random(),
        ];

        yield 'new organism is born when none is present' => [
            'expectedOrganismInCell' => 1,
            'originalOrganismInCell' => null,
            'neighbours' => [1, 2, 1, 1, 4, 2],
            'random' => new Random(),
        ];

        yield 'cell becomes empty if original cannot survive and non species can give birth' => [
            'expectedOrganismInCell' => null,
            'originalOrganismInCell' => 2,
            'neighbours' => [2, 1, 1],
            'random' => new Random(),
        ];

        yield 'cell stays empty if non species can give birth' => [
            'expectedOrganismInCell' => null,
            'originalOrganismInCell' => null,
            'neighbours' => [2, 1, 1],
            'random' => new Random(),
        ];

        yield 'random species is chosen when multiple can born' => [
            'expectedOrganismInCell' => 2,
            'originalOrganismInCell' => null,
            'neighbours' => [1, 2, 1, 2, 1, 2],
            'random' => $this->getRandomMock(),
        ];
    }

    /**
     * @param int[] $neighbours
     * @return Neighbours
     */
    private function getNeighboursFromNeighbourValues(array $neighbours): Neighbours
    {
        $neighbourCells = array_map(
            static function (int $organism): Cell {
                return new Cell($organism);
            },
            $neighbours
        );

        return new Neighbours($neighbourCells);
    }

    /**
     * @return Random&MockInterface
     */
    private function getRandomMock(): Random
    {
        $random = Mockery::mock(Random::class);
        $random->shouldReceive('getRandomArrayValue')->with([1, 2])->andReturn(2);

        return $random;
    }
}

(new CellTest())->run();
