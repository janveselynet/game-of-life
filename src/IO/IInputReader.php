<?php

namespace Life\IO;

use Life\Environment\World;


/**
 * Basic interface for classes reading game input from user
 */
interface IInputReader
{

    /**
     * @return World returns initial state of game world
     */
    public function getInitialWorld(): World;

    /**
     * @return int returns number of evolution steps game should do
     */
    public function getIterationsCount(): int;

}
