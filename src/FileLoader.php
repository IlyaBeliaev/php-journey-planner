<?php

namespace LJN;

use InvalidArgumentException;

/**
 * @author Linus Norton <linus.norton@assertis.co.uk>
 */
class FileLoader {

    public function getTimetableConnections($filename) {
        if ($handle = fopen($filename, "r")) {
            $timetable = [];

            while ((list($departureTime, $arrivalTime, $origin, $destination, $service) = fgetcsv($handle, 50, ",")) !== false) {
                $timetable[] = new TimetableConnection($origin, $destination, $departureTime, $arrivalTime, $service);
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

            while ((list($origin, $destination, $duration) = fgetcsv($handle, 50, ",")) !== false) {
                $connections[$origin][] = new NonTimetableConnection($origin, $destination, $duration);
            }

            return $connections;
        }
        else {
            throw new InvalidArgumentException("Could not open {$filename}");
        }
    }

    public function getInterchangeTimes($filename) {
        if ($handle = fopen($filename, "r")) {
            $interchangeTimes = [];

            while ((list($station, $duration) = fgetcsv($handle, 20, ",")) !== false) {
                $interchangeTimes[$station] = $duration * 60;
            }

            return $interchangeTimes;
        }
        else {
            throw new InvalidArgumentException("Could not open {$filename}");
        }
    }

    /**
     * @param string $filename
     * @return array
     */
    public function getLocations($filename)
    {
        $handle = fopen($filename, "r");

        if (empty($handle)) {
            throw new InvalidArgumentException("Could not open {$filename}");
        }

        $locations = [];

        while ((list($nlc, $name) = fgetcsv($handle, 200, ",")) !== false) {
            $locations[$nlc] = $name;
        }

        return $locations;
    }
}
