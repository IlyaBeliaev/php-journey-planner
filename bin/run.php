#!/usr/bin/env php
<?php

require __DIR__ . "/../vendor/autoload.php";

use LJN\FileLoader;
use LJN\DatabaseLoader;
use LJN\ConnectionScanner;
use LJN\TimetableConnection;
use LJN\DijkstraShortestPath;
use LJN\TreePersistence;


// CBW = 5007
// CBE = 5164
// TBW = 5230
// RYE = 5024
// TON = 5229
// EUS = 1444
// BIR = 1215
$origin = "5007";
$destination = "5230";
$targetTime = strtotime('2016-04-20 07:50');


$loader = new FileLoader(__DIR__ . '/../assets/');

function outputTask($name, $fn)
{
    echo "# {$name}";
    $timeStart = microtime(true);
    $result = $fn();
    $timeEnd = microtime(true);
    $time = sprintf("%8s", round($timeEnd - $timeStart, 4));
    echo str_pad(" finished {$time}s #", 78 - strlen($name), ".", STR_PAD_LEFT) . "\n";

    return $result;
}

echo "\n".str_pad(" Journey Planner ", 80, "#", STR_PAD_BOTH)."\n";

$timetableConnections = outputTask("Loading timetable", function () use ($targetTime, $origin) {
    $pdo = new \PDO("mysql:dbname=ojp;host=127.0.0.1", "ojp", "ojp");
    $loader = new DatabaseLoader($pdo);

    return $loader->getTimetableConnections($targetTime, $origin);
});

$nonTimetableConnections = outputTask("Loading non timetable connections", function () use ($loader) {
    return $loader->getNonTimetableConnections();
});

$interchangeTimes = outputTask("Loading intechange", function () use ($loader) {
    return $loader->getInterchangeTimes();
});

$locations = outputTask("Loading locations", function () use ($loader) {
    return $loader->getLocations();
});

$scanner = new ConnectionScanner($timetableConnections, $nonTimetableConnections, $interchangeTimes);

echo "\nConnections: ".count($timetableConnections);

$timetableMemory = memory_get_peak_usage();

$route = outputTask("Plan journey", function () use ($scanner, $targetTime, $origin, $destination) {
    return $scanner->getRoute($origin, $destination, $targetTime);
});

echo "\n".str_pad(" Route ", 80, "#", STR_PAD_BOTH)."\n";

/** @var Connection $connection */
foreach ($route as $connection) {
    $origin = sprintf('%-20s', $locations[$connection->getOrigin()]);
    $destination = sprintf('%20s', $locations[$connection->getDestination()]);

    if ($connection instanceof TimetableConnection) {
        print
            date('Y-m-d H:i', $connection->getDepartureTime()).' '.$origin.' '.
            $connection->getService().' '.
            $destination.' '.date('Y-m-d H:i', $connection->getArrivalTime())."\n";
    }
    else {
        print
            $connection->getMode().
            " from ".$origin.
            " to ".$destination.
            " (".($connection->getDuration()/60)." minutes)\n";
    }
}

$allMemory = memory_get_peak_usage();

echo "\nPeak memory usage: " . number_format($allMemory / 1024 / 1024, 2) . "Mb\n";
echo "Timetable memory usage: " . number_format($timetableMemory / 1024 / 1024, 2) . "Mb\n\n";