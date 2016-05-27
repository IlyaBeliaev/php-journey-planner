<?php

namespace JourneyPlanner\Lib;

use PDO;

/**
 * @author Linus Norton <linus.norton@assertis.co.uk>
 */
class DatabaseLoader {

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
     * Get any connections that are relevant to this query
     *
     * @param  string $startTime e.g. 07:42:20
     * @param  string $origin    e.g. CHX
     * @return TimetableConnection[]
     */
    public function getTimetableConnections($startTime, $origin) {
        $stmt = $this->db->prepare("
            SELECT c.* FROM timetable_connection c
            JOIN shortest_path sp
              ON c.destination = sp.destination
              AND :origin = sp.origin
            WHERE departureTime > :startTime + sp.duration
        ");

        $stmt->execute([
            'startTime' => $startTime,
            'origin' => $origin
        ]);

        return $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'TimetableConnection', ['','','','','']);
    }

    /**
     * Grap all connections after the target time
     *
     * @param  string $startTime
     * @return TimetableConnection[]
     */
    public function getUnprunedTimetableConnections($startTime) {
        $stmt = $this->db->prepare("SELECT * FROM timetable_connection WHERE departureTime > :startTime");
        $stmt->execute(['startTime' => $startTime]);

        return $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'TimetableConnection', ['','','','','']);
    }

    /**
     * Return all the pre-cached fastest connections between two stops
     *
     * @return array
     */
    public function getFastestConnections() {
        $stmt = $this->db->query("SELECT * FROM fastest_connection");

        return $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, '    TimetableConnection', ['','','','','']);
    }

    public function getNonTimetableConnections() {
    }

    public function getInterchangeTimes() {
    }
}
