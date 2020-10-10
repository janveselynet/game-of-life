<?php declare(strict_types = 1);

$loader = include __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Life\\', __DIR__ . '/Life');

DG\BypassFinals::enable();
Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

@mkdir(__DIR__ . '/tmp');
define('TEMP_DIR', __DIR__ . '/tmp/' . getmypid());
Tester\Helpers::purge(TEMP_DIR);
