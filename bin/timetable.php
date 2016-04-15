#!/usr/bin/env php
<?php

use App\App;
use App\Command\Timetable;
use Symfony\Component\Console\Input\StringInput;

require __DIR__ . '/../vendor/autoload.php';

$input = $_SERVER['argv'];
$input[0] = Timetable::NAME;

(new App())->getConsole()->run(new StringInput(join(' ', $input)));
