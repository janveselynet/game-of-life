<?php

namespace Life;

use Life\Environment\World;
use Life\IO\IInputReader;
use Life\IO\IOutputWriter;
use Life\Utils\Random;
use Mockery\MockInterface;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class GameTest extends TestCase
{

    const ITERATION_COUNT = 5;

    /** @var Random|MockInterface */
    private $random;

    /** @var Game */
    private $game;

    protected function setUp()
    {
        $this->random = \Mockery::mock(Random::class);
        $this->game = new Game($this->random);
    }

    public function testRunningGame()
    {
        $worlds = $this->getWorldMocks();
        $input = $this->getInputMock($worlds[0]);
        $output = $this->getOutputMock($worlds[self::ITERATION_COUNT]);
        $finalWord = $this->game->run($input, $output);
        Assert::true($finalWord instanceof World);
    }

    /**
     * @return World[]|MockInterface[]
     */
    private function getWorldMocks()
    {
        $worlds = [];
        for ($i = 0; $i < self::ITERATION_COUNT + 1; $i++) {
            $world = \Mockery::mock(World::class);
            if ($i === 0) {
                $world->shouldReceive('evolve')->never();
            }
            else {
                $world->shouldReceive('evolve')
                    ->once()
                    ->with($this->random)
                    ->andReturn($worlds[0]);
            }
            array_unshift($worlds, $world);
        }
        return $worlds;
    }

    /**
     * @param World $initialWorld
     * @return IInputReader|MockInterface
     */
    private function getInputMock(World $initialWorld)
    {
        $input = \Mockery::mock(IInputReader::class);
        $input->shouldReceive('getInitialWorld')->once()->andReturn($initialWorld);
        $input->shouldReceive('getIterationsCount')->once()->andReturn(self::ITERATION_COUNT);
        return $input;
    }

    /**
     * @param World $lastWorld
     * @return IOutputWriter|MockInterface
     */
    private function getOutputMock(World $lastWorld)
    {
        $output = \Mockery::mock(IOutputWriter::class);
        $output->shouldReceive('saveWorld')->once()->with($lastWorld);
        return $output;
    }

}

(new GameTest())->run();
