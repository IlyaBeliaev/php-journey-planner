<?php

namespace LJN;

/**
 * @author Linus Norton <linusnorton@gmail.com>
 */
class ConnectionScanner {

    const ORIGIN_INDEX = 0;

    /**
     * Stores the list of connections
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
        $connections = $this->getConnections($origin, $departureTime);

        return $this->getRouteFromConnections($connections, $origin, $destination);
    }

    /**
     * Create a HashMap containing the best connections to each station. At present
     * the fastest connection is considered best.
     *
     * @param  string $startStation
     * @param  int $startTime
     * @return array
     */
    private function getConnections($startStation, $startTime) {
        // this HashMap connections the earliest arrival time at each station, it's used for convenience
        // when comparing connections with each other.
        $arrivals = [$startStation => $startTime];

        // this HashMap contains the fastest available to connection to each station that can actually be
        // made based on previous connections.
        $connections = [];

        // check for non timetable connections at the origin station
        $this->checkForBetterNonTimetableConnections($connections, $arrivals, $startStation);

        foreach ($this->timetable as $connection) {
            list($origin, $destination, $departureTime, $arrivalTime) = $connection;

            $canGetToThisConnection = array_key_exists($origin, $arrivals) && $departureTime > $arrivals[$origin];
            $thisConnectionIsBetter = !array_key_exists($destination, $arrivals) || $arrivals[$destination] > $arrivalTime;

            if ($canGetToThisConnection && $thisConnectionIsBetter) {
                $arrivals[$destination] = $arrivalTime;
                $connections[$destination] = $connection;

                $this->checkForBetterNonTimetableConnections($connections, $arrivals, $destination);
            }
        }

        return $connections;
    }

    /**
     * For the given station for better non-timetabled connnections by calculating the potential arrival time
     * at the non timetabled connections destination as the arrival at the origin + the duration.
     *
     * In place of a mutable object $connections and $arrivals are passed by reference. This method doesn't have
     * a return value as it just mutates the array arguments.
     *
     * There is an assumption that the arrival at the given origin station can be made and as such $arrivals[$origin]
     * is set.
     *
     * @param array $connections
     * @param array $arrivals
     * @param string $origin
     */
    private function checkForBetterNonTimetableConnections(&$connections, &$arrivals, $origin) {
        // check if there is a non timetable connection starting at the destination, and process it's connections
        if (array_key_exists($origin, $this->nonTimetable)) {
            foreach ($this->nonTimetable[$origin] as list($destination, $duration)) {
                $thisConnectionIsBetter = !array_key_exists($destination, $arrivals) || $arrivals[$destination] > $arrivals[$origin] + $duration;

                if ($thisConnectionIsBetter) {
                    $arrivals[$destination] = $arrivals[$origin] + $duration;
                    $connections[$destination] = [$origin, $destination, $duration];
                }
            }
        }
    }
    /**
     * Given a Hash Map of fastest connections trace back the route from the target
     * destination to the origin. If no route is found an empty array is returned
     *
     * @param  array  $connections
     * @param  strubg $origin
     * @param  string $destination
     * @return array
     */
    private function getRouteFromConnections(array $connections, $origin, $destination) {
        $route = [];

        while (array_key_exists($destination, $connections)) {
            $route[] = $connections[$destination];
            $destination = $connections[$destination][self::ORIGIN_INDEX];
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
