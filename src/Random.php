<?php declare(strict_types = 1);

namespace Life;

use function array_rand;

final class Random
{
    /**
     * @param array<mixed> $array
     * @return mixed
     */
    public function getRandomArrayValue(array $array)
    {
        return $array[array_rand($array)];
    }
}
