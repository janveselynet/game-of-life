<?php declare(strict_types=1);

namespace Life\Environment;

use Life\Utils\Random;

final class Cell
{

    /**
     * @var int|null speciesCount of organism that lives in this cell, null if there is no organism
     */
    private ?int $organism;

    public function __construct(?int $organism = null)
    {
        $this->organism = $organism;
    }

    public function getOrganism(): ?int
    {
        return $this->organism;
    }

    public function hasOrganism(): bool
    {
        return $this->organism !== null;
    }

    public function evolve(Neighbours $neighbours, Random $random): Cell
    {
        if ($this->hasOrganism() && $neighbours->canSpeciesSurvive($this->organism)) {
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
