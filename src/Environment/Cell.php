<?php declare(strict_types=1);

namespace Life\Environment;

use Life\Random;

final class Cell
{
    /**
     * @var int|null species of organism that lives in this cell, null if there is no organism
     */
    private ?int $organismSpecies;

    public function __construct(?int $organismSpecies = null)
    {
        $this->organismSpecies = $organismSpecies;
    }

    public function getOrganismSpecies(): ?int
    {
        return $this->organismSpecies;
    }

    public function hasOrganism(): bool
    {
        return $this->organismSpecies !== null;
    }

    public function evolve(Neighbours $neighbours, Random $random): Cell
    {
        if ($this->organismSpecies !== null && $neighbours->canSpeciesSurvive($this->organismSpecies)) {
            return $this;
        }

        $speciesForBirth = $neighbours->getSpeciesForBirth();
        if (count($speciesForBirth) > 0) {
            $newSpecies = $random->getRandomArrayValue($speciesForBirth);
            return new Cell($newSpecies);
        }

        return new Cell();
    }
}
