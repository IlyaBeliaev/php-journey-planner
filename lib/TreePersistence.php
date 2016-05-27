<?php

namespace JourneyPlanner\Lib;

use PDO;
use Spork\ProcessManager;

class TreePersistence {

    /**
     * @var PDO
     */
    private $db;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    /**
     * Truncate the fastest_connection table and repopulate it by checking the timetable_connection
     * table for the fastest way to get from A to B
     */
    public function populateFastestConnections() {
        $this->db->exec("TRUNCATE fastest_connection");
        $this->db->exec("INSERT INTO fastest_connection SELECT * FROM timetable_connection GROUP BY origin, destination HAVING MIN(arrivalTime - departureTime)");
    }

    /**
     * Use the $pathFinder to store the shortest path tree for every stop in the graph
     *
     * @param  DijkstraShortestPath $pathFinder
     */
    public function populateShortestPaths(DijkstraShortestPath $pathFinder) {
        $stmt = $this->db->prepare("INSERT INTO shortest_path VALUES (:origin, :destination, :duration)");
        $manager = new ProcessManager();

        foreach ($pathFinder->getNodes() as $station) {
            $manager->fork(function() use ($pathFinder, $station, $stmt) {
                $tree = $pathFinder->getShortestPathTree($station);

                foreach ($tree as $destination => $duration) {
                    $stmt->execute([
                        "origin" => $station,
                        "destination" => $destination,
                        "duration" => $duration
                    ]);
                }
            });
        }
    }
}
