<?php declare(strict_types = 1);

namespace Life\Environment;

use Life\TestCase;
use Life\Random;
use Mockery;
use Mockery\Matcher\Closure;
use Tester\Assert;
use function count;
use function sprintf;

require_once __DIR__ . '/../../bootstrap.php';

final class WorldTest extends TestCase
{
    private const WORLD_SIZE = 3;
    private const SPECIES_COUNT = 5;

    public function testWorldEvolution(): void
    {
        $random = new Random();
        $cells = $this->createSampleCells($random);
        $world = new World(self::WORLD_SIZE, self::SPECIES_COUNT, $cells);

        $evolvedWorld = $world->evolve($random);

        Assert::same(self::WORLD_SIZE, $evolvedWorld->getSize());
        Assert::same(self::SPECIES_COUNT, $evolvedWorld->getSpeciesCount());

        $evolvedCells = $evolvedWorld->getCells();
        $this->assertAllCellsAreSet($evolvedCells);
    }

    /**
     * @param array<array<Cell>> $cells
     */
    private function assertAllCellsAreSet(array $cells): void
    {
        for ($y = 0; $y < self::WORLD_SIZE; $y++) {
            Assert::true(isset($cells[$y]), sprintf('Cells should have row with index %s', $y));
            for ($x = 0; $x < self::WORLD_SIZE; $x++) {
                Assert::true(isset($cells[$y][$x]), sprintf('Cells row %s should have cell with index %s', $y, $x));
            }
        }
    }

    /**
     * @return array<array<Cell>> $cells
     */
    private function createSampleCells(Random $random): array
    {
        $expectedNeighboursCount = [
            [3, 5, 3],
            [5, 8, 5],
            [3, 5, 3],
        ];

        $cells = [];
        for ($y = 0; $y < self::WORLD_SIZE; $y++) {
            $cells[$y] = [];
            for ($x = 0; $x < self::WORLD_SIZE; $x++) {
                $cell = Mockery::mock(Cell::class);
                $expectedCellNeighboursCount = $expectedNeighboursCount[$y][$x];

                $cell->shouldReceive('getOrganismSpecies')->andReturn(1);
                $cell->shouldReceive('evolve')
                    ->once()
                    ->with($this->getNeighboursCountMatcher($expectedCellNeighboursCount), $random)
                    ->andReturn(Mockery::mock(Cell::class));

                $cells[$y][$x] = $cell;
            }
        }

        return $cells;
    }

    private function getNeighboursCountMatcher(int $expectedNumberOfNeighbours): Closure
    {
        return Mockery::on(
            static function (Neighbours $neighbours) use ($expectedNumberOfNeighbours): bool {
                return count($neighbours->getNeighbours()) === $expectedNumberOfNeighbours;
            }
        );
    }
}

(new WorldTest())->run();
