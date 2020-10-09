<?php declare(strict_types=1);

namespace Life;

use Life\Environment\World;
use Life\IO\IInputReader;
use Life\IO\IOutputWriter;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

final class GameTest extends TestCase
{
    private const ITERATION_COUNT = 5;

    public function testRunningGame(): void
    {
        $game = new Game(new Random());
        $worlds = $this->getWorldMocksForEvolution(self::ITERATION_COUNT);

        $input = $this->getInputMock($worlds[0], self::ITERATION_COUNT);
        $output = $this->getOutputMock($worlds[self::ITERATION_COUNT]);

        $finalWord = $game->run($input, $output);

        Assert::true($finalWord instanceof World);
    }

    /**
     * @param int $iterationsCount
     * @return World[]|MockInterface[]
     */
    private function getWorldMocksForEvolution(int $iterationsCount): array
    {
        $worlds = [];

        for ($i = 0; $i < $iterationsCount + 1; $i++) {
            $world = Mockery::mock(World::class);

            if ($i === 0) {
                $world->shouldReceive('evolve')->never();

            } else {
                $world->shouldReceive('evolve')
                    ->once()
                    ->andReturn($worlds[0]);
            }

            array_unshift($worlds, $world);
        }

        return $worlds;
    }

    /**
     * @param World $initialWorld
     * @param int $iterationsCount
     * @return IInputReader&MockInterface
     */
    private function getInputMock(World $initialWorld, int $iterationsCount): IInputReader
    {
        $input = Mockery::mock(IInputReader::class);
        $input->shouldReceive('getInitialWorld')->once()->andReturn($initialWorld);
        $input->shouldReceive('getIterationsCount')->once()->andReturn($iterationsCount);

        return $input;
    }

    /**
     * @param World $lastWorld
     * @return IOutputWriter&MockInterface
     */
    private function getOutputMock(World $lastWorld): IOutputWriter
    {
        $output = Mockery::mock(IOutputWriter::class);
        $output->shouldReceive('saveWorld')->once()->with($lastWorld);

        return $output;
    }
}

(new GameTest())->run();
