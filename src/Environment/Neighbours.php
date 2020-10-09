<?php declare(strict_types=1);

namespace Life\Environment;

/**
 * Representing neighbours of concrete cell, computes some basic statistics about neighbours useful for evolution
 */
final class Neighbours
{

    /** @var Cell[] */
    private $neighbours;

    /** @var array where key is species identifier and value is number of available organisms with this species */
    private $availableSpeciesCounts;

    /**
     * @param Cell[] $neighbours
     */
    public function __construct(array $neighbours)
    {
        $this->neighbours = $neighbours;
        $this->availableSpeciesCounts = $this->computeAvailableSpeciesCounts($neighbours);
    }

    /**
     * @return Cell[]
     */
    public function getNeighbours(): array
    {
        return $this->neighbours;
    }

    /**
     * @return int[] get species identifiers that are present in neighbours
     */
    public function getAvailableSpecies(): array
    {
        return array_keys($this->availableSpeciesCounts);
    }

    /**
     * @return int[] get species identifiers that are present in neighbours and can born new cell
     */
    public function getSpeciesForBirth(): array
    {
        return array_values(
            array_filter($this->getAvailableSpecies(), function($species) {
                return $this->getSpeciesCount($species) === 3;
            })
        );
    }

    /**
     * @param int $species
     * @returns bool if given species has correct count of neighbours of same type to survive
     */
    public function canSpeciesSurvive(int $species): bool
    {
        $sameNeighboursCount = $this->getSpeciesCount($species);
        return $sameNeighboursCount >= 2 && $sameNeighboursCount <= 3;
    }

    /**
     * @param int $species species identifier
     * @return int number of neighbours of given species
     */
    public function getSpeciesCount(int $species): int
    {
        return isset($this->availableSpeciesCounts[$species]) ? $this->availableSpeciesCounts[$species] : 0;
    }

    /**
     * @param Cell[] $neighbours
     * @return array
     */
    private function computeAvailableSpeciesCounts(array $neighbours): array
    {
        $counts = [];
        foreach ($neighbours as $neighbour) {
            $species = $neighbour->getOrganism();
            if ($species !== null) {
                $counts[$species] = $counts[$species] ?? 0;
                $counts[$species]++;
            }
        }
        return $counts;
    }

}
