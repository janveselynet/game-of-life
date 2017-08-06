<?php

namespace Life\Environment;

use Life\Utils\Random;

/**
 * Representing world, holding size and available cells
 */
class World
{

    /** @var int dimension of the world */
    private $size;

    /**
     * @var Cell[][]
     * Array of available cells in the world with size x size dimensions
     * Indexed by y coordinate and then x coordinate
     */
    private $cells;

    /**
     * @param int $size
     * @param Cell[][] $cells
     */
    public function __construct($size, array $cells)
    {
        $this->size = $size;
        $this->cells = $cells;
    }

    /**
     * @return int return the dimension of the world
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return Cell[][] available cells indexed by y and x coordinates
     */
    public function getCells(): array
    {
        return $this->cells;
    }

    /**
     * @param Random $random
     * @return World resulting world after one iteration
     */
    public function evolve(Random $random): World
    {
        $newCells = [];
        for ($y = 0; $y < $this->size; $y++) {
            $newCells[] = [];
            for ($x = 0; $x < $this->size; $x++) {
                $newCells[$y][$x] = $this->evolveCell($x, $y, $random);
            }
        }
        return new World($this->size, $newCells);
    }

    /**
     * @param int $x
     * @param int $y
     * @param Random $random
     * @return Cell new cell evolved from the old one
     */
    private function evolveCell(int $x, int $y, Random $random): Cell
    {
        $cell = $this->cells[$x][$y];
        $neighbours = $this->getNeighbours($x, $y);
        return $cell->evolve($neighbours, $random);
    }

    /**
     * @param int $x
     * @param int $y
     * @return Neighbours
     */
    private function getNeighbours(int $x, int $y): Neighbours
    {
        $neighbours = [];
        $minY = max($y - 1, 0);
        $maxY = min($y + 1, $this->size - 1);
        $minX = max($x - 1, 0);
        $maxX = min($x + 1, $this->size - 1);
        for ($neighbourY = $minY; $neighbourY <= $maxY; $neighbourY++) {
            for ($neighbourX = $minX; $neighbourX <= $maxX; $neighbourX++) {
                if ($neighbourX !== $x || $neighbourY !== $y) {
                    $neighbours[] = $this->cells[$neighbourY][$neighbourX];
                }
            }
        }
        return new Neighbours($neighbours);
    }

}