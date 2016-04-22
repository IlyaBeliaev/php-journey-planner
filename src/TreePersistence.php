<?php

namespace LJN;

use PDO;

class TreePersistence {
    
    /**
     * @var PDO
     */
    private $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    public function populateFastestConnections() {
        $this->db->exec("TRUNCATE fastest_connection");
        $this->db->exec("INSERT INTO fastest_connection SELECT * FROM timetable_connection GROUP BY origin, destination HAVING MIN(arrivalTime - departureTime)");
    }

    public function populateShortestPaths(DijkstraShortestPath $pathFinder) {
        $stmt = $this->db->prepare("INSERT INTO shortest_path VALUES (:origin, :destination, :duration)");
        foreach ($pathFinder->getNodes() as $station) {
            $tree = $pathFinder->getShortestPathTree($station);

            foreach ($tree as $destination => $duration) {
                $stmt->execute([ 
                    "origin" => $station,
                    "destination" => $destination,
                    "duration" => $duration
                ]);
            }
        }
    }
}