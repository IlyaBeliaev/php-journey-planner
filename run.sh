#!/usr/bin/php5
<?php

require "vendor/autoload.php";

use LJN\FileLoader;
use LJN\ConnectionScanner;

$loader = new FileLoader();

function outputTask($name, $fn) {
    echo str_pad(" {$name} ", 80, "#", STR_PAD_BOTH)."\n";
    $timeStart = microtime(true);
    $result = $fn();
    $timeEnd = microtime(true);
    $time = round($timeEnd - $timeStart, 4);
    echo str_pad(" Done ({$time}) ", 80, "#", STR_PAD_BOTH)."\n\n";

    return $result;
}

$timetableConnections = outputTask("Load timetable", function () use ($loader) {
    return $loader->getTimetableConnections("assets/timetable.csv");
});

$nonTimetableConnections = outputTask("Load non timetable connections", function () use ($loader) {
    return $loader->getNonTimetableConnections("assets/non-timetable.csv");
});

$interchangeTimes = outputTask("Load intechange", function () use ($loader) {
    return $loader->getInterchangeTimes("assets/interchange.csv");
});

$scanner = new ConnectionScanner($timetableConnections, $nonTimetableConnections, $interchangeTimes);

print_r($scanner->getRoute("0072", "9000", 1460705000));
