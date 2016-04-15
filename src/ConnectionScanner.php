<?php

namespace LJN;

/**
 * @author Linus Norton <linusnorton@gmail.com>
 */
class ConnectionScanner {

    const ORIGIN_INDEX = 0;

    /**
     * Stores the list of connections. Please note that this timetable must be time ordered
     *
     * @var Array
     */
    private $timetable;

    /**
     * Stores the list of non timetabled connections
     *
     * @var Array
     */
    private $nonTimetable;

    /**
     * this HashMap contains the fastest available to connection to each station that can actually be
     * made based on previous connections.
     *
     * @var Array
     */
    private $connections;

    /**
     * this HashMap connections the earliest arrival time at each station, it's used for convenience
     * when comparing connections with each other.
     *
     * @var Array
     */
    private $arrivals;


    /**
     * @param array $timetable
     * @param array $nonTimetable
     */
    public function __construct(array $timetable, array $nonTimetable) {
        $this->timetable = $timetable;
        $this->nonTimetable = $nonTimetable;
    }

    /**
     * Use the connection scan algorithm to find the fastest path from $origin to
     * $destination
     *
     * @param  string $origin
     * @param  string $destination
     * @param  string $departureTime
     * @return array
     */
    public function getRoute($origin, $destination, $departureTime) {
        $this->arrivals = [$origin => $departureTime];
        $this->connections = [];

        $this->getConnections($origin);

        return $this->getRouteFromConnections($origin, $destination);
    }

    /**
     * Create a HashMap containing the best connections to each station. At present
     * the fastest connection is considered best.
     *
     * @param  string $startStation
     * @param  int $startTime
     */
    private function getConnections($startStation) {
        // check for non timetable connections at the origin station
        $this->checkForBetterNonTimetableConnections($startStation);

        foreach ($this->timetable as $connection) {
            list($origin, $destination, $departureTime, $arrivalTime) = $connection;

            $canGetToThisConnection = array_key_exists($origin, $this->arrivals) && $departureTime > $this->arrivals[$origin];
            $thisConnectionIsBetter = !array_key_exists($destination, $this->arrivals) || $this->arrivals[$destination] > $arrivalTime;

            if ($canGetToThisConnection && $thisConnectionIsBetter) {
                $this->arrivals[$destination] = $arrivalTime;
                $this->connections[$destination] = $connection;

                $this->checkForBetterNonTimetableConnections($destination);
            }
        }
    }

    /**
     * For the given station for better non-timetabled connnections by calculating the potential arrival time
     * at the non timetabled connections destination as the arrival at the origin + the duration.
     *
     * There is an assumption that the arrival at the given origin station can be made and as such $this->arrivals[$origin]
     * is set.
     *
     * @param string $origin
     */
    private function checkForBetterNonTimetableConnections($origin) {
        // check if there is a non timetable connection starting at the destination, and process it's connections
        if (array_key_exists($origin, $this->nonTimetable)) {
            foreach ($this->nonTimetable[$origin] as list($destination, $duration)) {
                $thisConnectionIsBetter = !array_key_exists($destination, $this->arrivals) || $this->arrivals[$destination] > $this->arrivals[$origin] + $duration;

                if ($thisConnectionIsBetter) {
                    $this->arrivals[$destination] = $this->arrivals[$origin] + $duration;
                    $this->connections[$destination] = [$origin, $destination, $duration];
                }
            }
        }
    }
    /**
     * Given a Hash Map of fastest connections trace back the route from the target
     * destination to the origin. If no route is found an empty array is returned
     *
     * @param  strubg $origin
     * @param  string $destination
     * @return array
     */
    private function getRouteFromConnections($origin, $destination) {
        $route = [];

        while (array_key_exists($destination, $this->connections)) {
            $route[] = $this->connections[$destination];
            $destination = $this->connections[$destination][self::ORIGIN_INDEX];
        }

        // if we found a route back to the origin
        if ($origin === $destination) {
            return array_reverse($route);
        }
        else {
            return [];
        }

    }
}
