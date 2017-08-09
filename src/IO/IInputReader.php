<?php

namespace Life\IO;

use Life\Environment\World;
use Life\Exceptions\InvalidInputException;

/**
 * Basic interface for classes reading game input from user
 */
interface IInputReader
{

    /**
     * @return World returns initial state of game world
     * @throws InvalidInputException when something goes wrong during input reading
     */
    public function getInitialWorld(): World;

    /**
     * @return int returns number of evolution steps game should do
     * @throws InvalidInputException when something goes wrong during input reading
     */
    public function getIterationsCount(): int;

}
