<?php

namespace Life\Environment;

use Life\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

class NeighboursTest extends TestCase
{

    /** @var Cell[] */
    private $cells;

    /** @var Neighbours */
    private $neighbours;

    protected function setUp()
    {
        parent::setUp();
        $this->cells = [
            new Cell(1),
            new Cell(2),
            new Cell(1),
            new Cell(1),
            new Cell(4),
            new Cell(2),
        ];
        $this->neighbours = new Neighbours($this->cells);
    }

    public function testGettingNeighbours()
    {
        Assert::same($this->cells, $this->neighbours->getNeighbours());
    }

    public function testGettingAvailableSpecies()
    {
        Assert::equal([1, 2, 4], $this->neighbours->getAvailableSpecies());
    }

    public function testGettingSpeciesForBirth()
    {
        $this->cells[] = new Cell(2);
        $this->cells[] = new Cell(4);
        $this->cells[] = new Cell(4);
        $this->cells[] = new Cell(5);
        $this->cells[] = new Cell(4);
        $neighbours = new Neighbours($this->cells);
        Assert::same([1, 2], $neighbours->getSpeciesForBirth());
    }

    public function testGettingIfSpeciesCanSurvive()
    {
        Assert::true($this->neighbours->canSpeciesSurvive(1));
        Assert::true($this->neighbours->canSpeciesSurvive(2));
        Assert::false($this->neighbours->canSpeciesSurvive(3));
        Assert::false($this->neighbours->canSpeciesSurvive(4));
    }

    public function testGettingCountsOfAvailableSpecies()
    {
        Assert::same(3, $this->neighbours->getSpeciesCount(1));
        Assert::same(2, $this->neighbours->getSpeciesCount(2));
        Assert::same(1, $this->neighbours->getSpeciesCount(4));
    }

    public function testThatCountOfUnavailableSpeciesIsZero()
    {
        Assert::same(0, $this->neighbours->getSpeciesCount(0));
        Assert::same(0, $this->neighbours->getSpeciesCount(3));
        Assert::same(0, $this->neighbours->getSpeciesCount(150));
    }

}

(new NeighboursTest())->run();
