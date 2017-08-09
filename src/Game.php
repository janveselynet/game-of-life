<?php

namespace Life;

use Life\Environment\World;
use Life\Exceptions\InvalidInputException;
use Life\Exceptions\OutputWritingException;
use Life\IO\IInputReader;
use Life\IO\IOutputWriter;
use Life\Utils\Random;

/**
 * Class controlling game play: reads input, runs given number of evolutions and saves result
 */
class Game
{

    /** @var Random */
    private $random;

    /**
     * @param Random $random
     */
    public function __construct(Random $random)
    {
        $this->random = $random;
    }

    /**
     * @param IInputReader $input
     * @param IOutputWriter $outputWriter
     * @return World returns final state of world
     * @throws InvalidInputException when something goes wrong during input reading
     * @throws OutputWritingException when writing output failed for some reason
     */
    public function run(IInputReader $input, IOutputWriter $outputWriter): World
    {
        $world = $input->getInitialWorld();
        $iterations = $input->getIterationsCount();
        for ($i = 0; $i < $iterations; $i++) {
            $world = $world->evolve($this->random);
        }
        $outputWriter->saveWorld($world);
        return $world;
    }

}
