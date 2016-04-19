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

    public function getTimetableConnections($startDate) {
        $stmt = $this->db->prepare("
            SELECT * FROM timetable_connection WHERE departureTime > :startDate
        ");

        $stmt->execute(['startDate' => $startDate]);

        return $stmt->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'LJN\TimetableConnection', ['','','','','']);
    }

}
