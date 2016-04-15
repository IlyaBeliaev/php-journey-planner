<?php

require "vendor/autoload.php";

use LJN\FileLoader;
use LJN\ConnectionScanner;
use LJN\TimetableConnection;

$loader = new FileLoader();

function outputTask($name, $fn)
{
    echo str_pad(" {$name} ", 80, "#", STR_PAD_BOTH) . "\n";
    $timeStart = microtime(true);
    $result = $fn();
    $timeEnd = microtime(true);
    $time = round($timeEnd - $timeStart, 4);
    echo str_pad(" Done ({$time}) ", 80, "#", STR_PAD_BOTH) . "\n\n";

    return $result;
}

$timetableConnections = outputTask("Load timetable", function () use ($loader) {
    return $loader->getTimetableConnections("assets/test-sorted.csv");
});

$nonTimetableConnections = outputTask("Load non timetable connections", function () use ($loader) {
    return $loader->getNonTimetableConnections("assets/non-timetable.csv");
});

$interchangeTimes = outputTask("Load intechange", function () use ($loader) {
    return $loader->getInterchangeTimes("assets/interchange.csv");
});

$locations = outputTask("Load locations", function () use ($loader) {
    return $loader->getLocations("assets/locations.csv");
});

$scanner = new ConnectionScanner($timetableConnections, $nonTimetableConnections, $interchangeTimes);

/** @var TimetableConnection $connection */
foreach ($scanner->getRoute('5224', '5228', strtotime('2016-04-20')) as $connection) {
    $origin = sprintf('%-20s', $locations[$connection->getOrigin()]);
    $destination = sprintf('%20s', $locations[$connection->getDestination()]);

    print
        date('Y-m-d H:i', $connection->getDepartureTime()).' '.$origin.' '.
        $connection->getService().' '.
        $destination.' '.date('Y-m-d H:i', $connection->getArrivalTime())."\n";
}

//print_r($scanner->getRoute('5224', '5228', strtotime('2016-04-20')));
