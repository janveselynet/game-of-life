<?php declare(strict_types=1);

namespace Life\IO;

use Life\Environment\World;

interface IInputReader
{

    /**
     * @return World
     * @throws InvalidInputException
     */
    public function getInitialWorld(): World;

    /**
     * @return int
     * @throws InvalidInputException
     */
    public function getIterationsCount(): int;

}
