<?php

namespace Life\Environment;

use Life\TestCase;
use Life\Utils\Random;
use Mockery\MockInterface;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

class WorldTest extends TestCase
{

    const WORLD_SIZE = 3;

    /** @var Cell[][] */
    private $cells;

    /** @var World */
    private $world;

    /** @var Random */
    private $random;

    protected function setUp()
    {
        $this->cells = [
            [$this->createMockCell(), $this->createMockCell(), $this->createMockCell()],
            [$this->createMockCell(),$this->createMockCell(),$this->createMockCell()],
            [$this->createMockCell(), $this->createMockCell(), $this->createMockCell()],
        ];
        $this->world = new World(self::WORLD_SIZE, $this->cells);
        $this->random = \Mockery::mock(Random::class);
    }

    public function testGettingSize()
    {
        Assert::same(self::WORLD_SIZE, $this->world->getSize());
    }

    public function testGettingCells()
    {
        $cells = $this->world->getCells();
        $this->checkAllCellsAreSet($cells);
    }

    public function testEvolution()
    {
        $this->setEvolutionExpectationsOnCells();
        $newWorld = $this->world->evolve($this->random);
        Assert::true($newWorld instanceof World);
        Assert::same(self::WORLD_SIZE, $newWorld->getSize());
        $newCells = $newWorld->getCells();
        $this->checkAllCellsAreSet($newCells);
    }

    /**
     * @param Cell[][] $cells
     * @return void
     */
    private function checkAllCellsAreSet(array $cells)
    {
        Assert::true(is_array($cells));
        for ($y = 0; $y < self::WORLD_SIZE; $y++) {
            Assert::true(isset($cells[$y]));
            for ($x = 0; $x < self::WORLD_SIZE; $x++) {
                Assert::true(isset($cells[$y][$x]));
                Assert::true($cells[$y][$x] instanceof Cell);
            }
        }
    }

    private function createMockCell()
    {
        return \Mockery::mock(Cell::class);
    }

    private function setEvolutionExpectationsOnCells()
    {
        $neighboursCount = [
            [3, 5, 3],
            [5, 8, 5],
            [3, 5, 3],
        ];
        for ($y = 0; $y < self::WORLD_SIZE; $y++) {
            for ($x = 0; $x < self::WORLD_SIZE; $x++) {
                $cell = $this->cells[$y][$x]; /** @var MockInterface $cellNeighboursCount */
                $cellNeighboursCount = $neighboursCount[$y][$x];
                $cell->shouldReceive('getOrganism')->andReturn(1);
                $cell->shouldReceive('evolve')
                    ->once()
                    ->with(
                        \Mockery::on(function($neighboursArgument) use($cellNeighboursCount) {
                            if (!($neighboursArgument instanceof Neighbours)) {
                                return false;
                            }
                            return count($neighboursArgument->getNeighbours()) === $cellNeighboursCount;
                        }),
                        $this->random
                    )
                    ->andReturn(\Mockery::mock(Cell::class));
            }
        }
    }

}

(new WorldTest())->run();