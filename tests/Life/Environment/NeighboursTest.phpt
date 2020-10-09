<?php declare(strict_types=1);

namespace Life\Environment;

use Generator;
use Life\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

final class NeighboursTest extends TestCase
{
    public function testGettingAvailableSpecies(): void
    {
        $neighbours = $this->createSampleNeighbours();

        $availableSpecies = $neighbours->getAvailableSpecies();

        Assert::same([1, 2, 4], $availableSpecies);
    }

    public function testGettingSpeciesForBirth()
    {
        $neighbours = $this->createSampleNeighboursForBirth();

        $speciesForBirth = $neighbours->getSpeciesForBirth();

        Assert::same([1, 2], $speciesForBirth);
    }

    /**
     * @dataProvider speciesCanSurviveProvider
     * @param bool $expectedResult
     * @param int $species
     */
    public function testGettingIfSpeciesCanSurvive(bool $expectedResult, int $species)
    {
        $neighbours = $this->createSampleNeighbours();

        $actualResult = $neighbours->canSpeciesSurvive($species);

        Assert::same($expectedResult, $actualResult);
    }

    public function speciesCanSurviveProvider(): Generator
    {
        yield 'can survive #1' => [
            'expectedResult' => true,
            'species' => 1,
        ];

        yield 'can survive #2' => [
            'expectedResult' => true,
            'species' => 2,
        ];

        yield 'cannot survive #1' => [
            'expectedResult' => false,
            'species' => 3,
        ];

        yield 'cannot survive #2' => [
            'expectedResult' => false,
            'species' => 4,
        ];
    }


    /**
     * @dataProvider countOfAvailableSpeciesProvider
     * @param int $expectedCount
     * @param int $species
     */
    public function testGettingCountsOfAvailableSpecies(int $expectedCount, int $species)
    {
        $neighbours = $this->createSampleNeighbours();

        $actualCount = $neighbours->getSpeciesCount($species);

        Assert::same($expectedCount, $actualCount);
    }

    public function countOfAvailableSpeciesProvider(): Generator
    {
        yield 'species 1' => [
            'expectedCount' => 3,
            'species' => 1,
        ];

        yield 'species 2' => [
            'expectedCount' => 2,
            'species' => 2,
        ];

        yield 'species 4' => [
            'expectedCount' => 1,
            'species' => 4,
        ];

        yield 'unknown species' => [
            'expectedCount' => 0,
            'species' => 150,
        ];
    }

    private function createSampleNeighbours(): Neighbours
    {
        $cells = [
            new Cell(1),
            new Cell(2),
            new Cell(1),
            new Cell(1),
            new Cell(4),
            new Cell(2),
        ];

        return new Neighbours($cells);
    }

    private function createSampleNeighboursForBirth(): Neighbours
    {
        $cells = [
            new Cell(1),
            new Cell(2),
            new Cell(1),
            new Cell(1),
            new Cell(4),
            new Cell(2),
            new Cell(2),
            new Cell(4),
            new Cell(4),
            new Cell(5),
            new Cell(4),
        ];

        return new Neighbours($cells);
    }
}

(new NeighboursTest())->run();
