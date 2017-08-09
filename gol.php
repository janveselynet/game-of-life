<?php

require './vendor/autoload.php';

$command = new \Commando\Command();

$command->option('i')
    ->aka('input')
    ->describe('Input XML file')
    ->require();

$command->option('o')
    ->aka('output')
    ->describe('Output XML file')
    ->default('out.xml');

$random = new \Life\Utils\Random();
$game = new \Life\Game($random);

$input = new \Life\IO\XmlFileReader($command['input'], $random);
$output = new \Life\IO\XmlFileWriter($command['output']);

$game->run($input, $output);
