<?php

namespace LJN;

/**
 * @author Linus Norton <linus.norton@assertis.co.uk>
 */
class FileLoader {

    public function getTimetableConnections($filename) {
        if ($handle = fopen($filename, "r")) {
            $timetable = [];

            while (($row = fgetcsv($handle, 100, ",")) !== false) {
                $timetable[] = $row;
            }

            return $timetable;
        }
        else {
            throw new InvalidArgumentException("Could not open {$filename}");
        }
    }

    public function getNonTimetableConnections($filename) {
        if ($handle = fopen($filename, "r")) {
            $connections = [];

            while ((list($origin, $destination, $duration) = fgetcsv($handle, 100, ",")) !== false) {
                $connections[$origin] = [$destination, $duration];
            }

            return $connections;
        }
        else {
            throw new InvalidArgumentException("Could not open {$filename}");
        }
    }

}
