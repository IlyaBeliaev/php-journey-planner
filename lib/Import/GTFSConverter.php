<?php

namespace JourneyPlanner\Lib\Import;

use PDO;
use Monolog\Logger;

class GTFSConverter {

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo, Logger $logger) {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    /**
     * Loop over all the stop_times and insert a corresponding timetable_connection
     */
    public function importTimetableConnections() {
        $this->pdo->exec("TRUNCATE timetable_connection");

        $stmt = $this->pdo->prepare("
            INSERT INTO timetable_connection
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $results = $this->pdo->query("
            SELECT
                departure_time,
                arrival_time,
                parent_station,
                trip_id,
                monday,
                tuesday,
                wednesday,
                thursday,
                friday,
                saturday,
                sunday,
                start_date,
                end_date
            FROM stop_times
            JOIN stops USING (stop_id)
            JOIN trips USING (trip_id)
            JOIN calendar USING (service_id)
            ORDER BY trip_id, stop_sequence
        ");

        $prev = null;

        while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
            if ($prev === null || $prev['trip_id'] !== $row['trip_id']) {
                $prev = $row;
                continue;
            }

            $stmt->execute([
                $prev['departure_time'],
                $row['arrival_time'],
                $prev['parent_station'],
                $row['parent_station'],
                $row['trip_id'],
                $row['monday'],
                $row['tuesday'],
                $row['wednesday'],
                $row['thursday'],
                $row['friday'],
                $row['saturday'],
                $row['sunday'],
                $row['start_date'],
                $row['end_date']
            ]);

            $this->logger->debug("Added connection between " . $prev["parent_station"] . " and ". $row["parent_station"]);
            $prev = $row;
        }
    }
}
