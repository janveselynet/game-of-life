<?php declare(strict_types = 1);

namespace Life\IO;

use Life\Environment\World;

interface IInputReader
{
    /**
     * @throws InvalidInputException
     */
    public function getInitialWorld(): World;

    /**
     * @throws InvalidInputException
     */
    public function getIterationsCount(): int;
}
