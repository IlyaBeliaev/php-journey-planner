#!/usr/bin/env php
<?php

use App\Command\Timetable;
use App\Console;
use Symfony\Component\Console\Input\StringInput;

require __DIR__ . '/../vendor/autoload.php';

$input = $_SERVER['argv'];
$input[0] = Timetable::NAME;

(new Console())->run(new StringInput(join(' ', $input)));
