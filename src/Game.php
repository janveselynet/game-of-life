<?php declare(strict_types=1);

namespace Life;

use Life\Environment\World;
use Life\IO\InvalidInputException;
use Life\IO\OutputWritingException;
use Life\IO\IInputReader;
use Life\IO\IOutputWriter;

final class Game
{
    private Random $random;

    public function __construct(Random $random)
    {
        $this->random = $random;
    }

    /**
     * @param IInputReader $input
     * @param IOutputWriter $outputWriter
     * @return World
     * @throws InvalidInputException
     * @throws OutputWritingException
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
