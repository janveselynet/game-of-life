<?php declare(strict_types=1);

namespace Life\Environment;

final class Neighbours
{
    /**
     * @var Cell[]
     */
    private array $neighbours;

    /**
     * @var int[] where key is species identifier and value is number of available organisms with this species
     */
    private array $availableSpeciesCounts;

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
     * @return int[]
     */
    public function getAvailableSpecies(): array
    {
        return array_keys($this->availableSpeciesCounts);
    }

    /**
     * @return int[]
     */
    public function getSpeciesForBirth(): array
    {
        return array_values(
            array_filter(
                $this->getAvailableSpecies(),
                function (int $species): bool {
                    return $this->getSpeciesCount($species) === 3;
                }
            )
        );
    }

    public function canSpeciesSurvive(int $species): bool
    {
        $sameNeighboursCount = $this->getSpeciesCount($species);
        return $sameNeighboursCount >= 2 && $sameNeighboursCount <= 3;
    }

    public function getSpeciesCount(int $species): int
    {
        return $this->availableSpeciesCounts[$species] ?? 0;
    }

    /**
     * @param Cell[] $neighbours
     * @return int[]
     */
    private function computeAvailableSpeciesCounts(array $neighbours): array
    {
        $counts = [];

        foreach ($neighbours as $neighbour) {
            $species = $neighbour->getOrganismSpecies();
            if ($species !== null) {
                $counts[$species] = $counts[$species] ?? 0;
                $counts[$species]++;
            }
        }

        return $counts;
    }
}
