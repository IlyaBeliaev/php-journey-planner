#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use \PDO;
use LJN\DatabaseLoader;
use LJN\DijkstraShortestPath;
use LJN\TreePersistence;

$pdo = new PDO("mysql:dbname=ojp;host=127.0.0.1", "ojp", "ojp");
$loader = new DatabaseLoader($pdo);
$treePersistence = new TreePersistence($pdo);

$treePersistence->populateFastestConnections();
echo "Fastest connections created\n";
$timetable = $loader->getFastestConnections();
echo "Connections loaded\n";
$pathFinder = new DijkstraShortestPath($timetable);
echo "Path finder created\n";
$treePersistence->populateShortestPaths($pathFinder);
echo "Shortest paths calculated\n";
