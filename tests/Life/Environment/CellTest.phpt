<?php declare(strict_types=1);

namespace Life\Environment;

use Life\TestCase;
use Life\Utils\Random;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

final class CellTest extends TestCase
{

    /** @var Neighbours */
    private $neighbours;

    /** @var Random */
    private $random;

    protected function setUp()
    {
        parent::setUp();
        $this->neighbours = new Neighbours([
            new Cell(1),
            new Cell(2),
            new Cell(1),
            new Cell(1),
            new Cell(4),
            new Cell(2),
        ]);
        $this->random = \Mockery::mock(Random::class);
        $this->random->shouldReceive('getRandomArrayValue')->with([1])->andReturn(1);
    }

    public function testGettingOrganism()
    {
        $organism = 10;
        $cell = new Cell($organism);
        Assert::same($organism, $cell->getOrganism());
    }

    public function testGettingNullIfNoOrganismOnCell()
    {
        $cell = new Cell();
        Assert::null($cell->getOrganism());
    }

    public function testGettingThatSomeOrganismIsOnCell()
    {
        $organism = 10;
        $cell = new Cell($organism);
        Assert::true($cell->hasOrganism());
    }

    public function testGettingThatNoOrganismIsOnCell()
    {
        $cell = new Cell();
        Assert::false($cell->hasOrganism());
    }

    public function testSurvivingEvolutionWhenCorrectNumberOfNeighbours()
    {
        $cell1 = new Cell(1);
        $this->evolveAndCheckSpecies(1, $cell1, $this->neighbours);
        $cell2 = new Cell(2);
        $this->evolveAndCheckSpecies(2, $cell2, $this->neighbours);
    }

    public function testEvolvingNewOrganismIfCurrentCantSurvive()
    {
        $cell = new Cell(4);
        $this->evolveAndCheckSpecies(1, $cell, $this->neighbours);
    }

    public function testEvolvingNewOrganismIfNoneIsPresent()
    {
        $cell = new Cell();
        $this->evolveAndCheckSpecies(1, $cell, $this->neighbours);
    }

    public function testEvolvingEmptyCellIfNoSpeciesCanGiveBirth()
    {
        $neighbours = new Neighbours([
            new Cell(2),
            new Cell(1),
            new Cell(1),
        ]);
        $cell1 = new Cell();
        $this->evolveAndCheckSpecies(null, $cell1, $neighbours);
        $cell2 = new Cell(2);
        $this->evolveAndCheckSpecies(null, $cell2, $neighbours);
    }

    public function testGettingRandomSpeciesWhenMoreCanGiveBirth()
    {
        $this->random = \Mockery::mock(Random::class);
        $this->random->shouldReceive('getRandomArrayValue')->with([1, 2])->andReturn(1, 2);
        $neighbours = new Neighbours([
            new Cell(1),
            new Cell(2),
            new Cell(1),
            new Cell(2),
            new Cell(1),
            new Cell(2),
        ]);
        $cell1 = new Cell();
        $this->evolveAndCheckSpecies(1, $cell1, $neighbours);
        $cell2 = new Cell();
        $this->evolveAndCheckSpecies(2, $cell2, $neighbours);
    }

    /**
     * @param int|null $expectedSpecies
     * @param Cell $cell
     * @param Neighbours $neighbours
     * @return void
     */
    private function evolveAndCheckSpecies($expectedSpecies, Cell $cell, Neighbours $neighbours)
    {
        $evolvedCell = $cell->evolve($neighbours, $this->random);
        Assert::same($expectedSpecies, $evolvedCell->getOrganism());
    }

}

(new CellTest())->run();
