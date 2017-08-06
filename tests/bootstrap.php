<?php

$loader = include __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Life\\', __DIR__ . '/Life');

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

define('TEMP_DIR', __DIR__ . '/tmp/' . getmypid());
Tester\Helpers::purge(TEMP_DIR);
