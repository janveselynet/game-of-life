<?php declare(strict_types = 1);

namespace Life;

use InvalidArgumentException;
use Life\IO\XmlFileReader;
use Life\IO\XmlFileWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function is_string;

final class RunGameCommand extends Command
{
    private const INPUT_OPTION = 'input';
    private const OUTPUT_OPTION = 'output';

    private Game $game;

    private Random $random;

    public function __construct(Game $game, Random $random)
    {
        parent::__construct('game:run');
        $this->game = $game;
        $this->random = $random;
    }

    protected function configure(): void
    {
        $this->setDescription('Runs game of life');
        $this->addOption(self::INPUT_OPTION, 'i', InputOption::VALUE_REQUIRED, 'Input XML file');
        $this->addOption(self::OUTPUT_OPTION, 'o', InputOption::VALUE_OPTIONAL, 'Output XML file', 'out.xml');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputFile = $input->getOption(self::INPUT_OPTION);
        if (!is_string($inputFile) || $inputFile === '') {
            throw new InvalidArgumentException('Invalid input value');
        }
        $inputReader = new XmlFileReader($inputFile, $this->random);

        $outputFile = $input->getOption(self::OUTPUT_OPTION);
        if (!is_string($outputFile)) {
            throw new InvalidArgumentException('Invalid output value');
        }
        $outputWriter = new XmlFileWriter($outputFile);

        $this->game->run($inputReader, $outputWriter);

        return Command::SUCCESS;
    }
}
