<?php

namespace LJN;

use PDO;

/**
 * @author Linus Norton <linus.norton@assertis.co.uk>
 */
class DatabaseLoader {

    /**
     * @var PDO
     */
    private $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    public function getTimetableConnections($startDate, $origin) {
        //SELECT * FROM timetable_connection WHERE departureTime > :startDate
        $stmt = $this->db->prepare("

            SELECT c.* FROM timetable_connection c 
            JOIN shortest_path sp 
              ON c.destination = sp.destination 
              AND :origin = sp.origin 
            WHERE departureTime > :startDate + sp.duration
            AND arrivalTime < :endDate
        ");

        $stmt->execute([
            'startDate' => $startDate,
            'origin' => $origin,
            'endDate' => $startDate + 12 * 60 * 60
        ]);

        return $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'LJN\TimetableConnection', ['','','','','']);
    }

    public function getFastestConnections() {
        $stmt = $this->db->query("SELECT * FROM fastest_connection");

        return $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'LJN\TimetableConnection', ['','','','','']);
    }    

    public function getNonTimetableConnections() {
    }

    public function getInterchangeTimes() {        
    }
}
