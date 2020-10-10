<?php

require './vendor/autoload.php';

use Life\Game;
use Life\Random;
use Life\RunGameCommand;
use Symfony\Component\Console\Application;

$random = new Random();
$game = new Game($random);
$runGameCommand = new RunGameCommand($game, $random);

$application = new Application();
$application->add($runGameCommand);

$application->run();
