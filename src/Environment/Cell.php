<?php

namespace Life\Environment;

use Life\Utils\Random;

/**
 * Representing one cell (element) in world, can be with or without organism
 */
class Cell
{

    /** @var int|null species of organism that lives in this cell, null if there is no organism */
    private $organism;

    /**
     * @param int|null $organism
     */
    public function __construct(int $organism = null)
    {
        $this->organism = $organism;
    }

    /**
     * @return int|null returns species of organism living in this cell, or null if there is no organism
     */
    public function getOrganism()
    {
        return $this->organism;
    }

    /**
     * @return bool returns if some organism lives in this cell
     */
    public function hasOrganism(): bool
    {
        return $this->organism !== null;
    }

    /**
     * @param Neighbours $neighbours
     * @param Random $random
     * @return Cell new cell that is evolved
     */
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
