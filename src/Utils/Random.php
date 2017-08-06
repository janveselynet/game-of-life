<?php

namespace Life\Utils;

/**
 * Methods fr working with randomness. Useful for mocking in tests.
 */
class Random
{

    /**
     * @param array $array
     * @return mixed return random value from given array
     */
    public function getRandomArrayValue(array $array)
    {
        return $array[array_rand($array)];
    }

}
