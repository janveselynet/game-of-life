<?php declare(strict_types=1);

namespace Life;

final class Random
{
    /**
     * @param mixed[] $array
     * @return mixed
     */
    public function getRandomArrayValue(array $array)
    {
        return $array[array_rand($array)];
    }
}
