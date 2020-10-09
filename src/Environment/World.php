<?php declare(strict_types=1);

namespace Life\Environment;

use Life\Random;

final class World
{
    private int $size;

    private int $speciesCount;

    /**
     * @var Cell[][]
     */
    private array $cells;

    /**
     * @param int $size
     * @param int $species
     * @param Cell[][] $cells
     */
    public function __construct(int $size, int $species, array $cells)
    {
        $this->size = $size;
        $this->speciesCount = $species;
        $this->cells = $cells;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getSpeciesCount(): int
    {
        return $this->speciesCount;
    }

    /**
     * @return Cell[][]
     */
    public function getCells(): array
    {
        return $this->cells;
    }

    public function evolve(Random $random): World
    {
        $newCells = [];

        for ($y = 0; $y < $this->size; $y++) {
            $newCells[] = [];
            for ($x = 0; $x < $this->size; $x++) {
                $newCells[$y][$x] = $this->evolveCell($x, $y, $random);
            }
        }

        return new World($this->size, $this->speciesCount, $newCells);
    }

    private function evolveCell(int $x, int $y, Random $random): Cell
    {
        $cell = $this->cells[$y][$x];
        $neighbours = $this->getNeighbours($x, $y);

        return $cell->evolve($neighbours, $random);
    }

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
