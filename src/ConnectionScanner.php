<?php

namespace LJN;

/**
 * @author Linus Norton <linusnorton@gmail.com>
 */
class ConnectionScanner {

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
     * HashMap storing the fastest available to connection to each station that can actually be
     * made based on previous connections.
     *
     * @var Array
     */
    private $connections;

    /**
     * HashMap storing each connections earliest arrival time, it's used for convenience
     * when comparing connections with each other.
     *
     * @var Array
     */
    private $arrivals;

    /**
     * HashMap of station => interchange time required at that station when changing service
     *
     * @var Array
     */
    private $interchangeTimes;

    /**
     * @param array $timetable
     * @param array $nonTimetable
     * @param array $interchangeTimes
     */
    public function __construct(array $timetable, array $nonTimetable, array $interchangeTimes) {
        $this->timetable = $timetable;
        $this->nonTimetable = $nonTimetable;
        $this->interchangeTimes = $interchangeTimes;
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
            list($origin, $destination, $departureTime, $arrivalTime) = $connection->toArray();

            $interchangeTime = array_key_exists($origin, $this->connections) && array_key_exists($origin, $this->interchangeTimes) && $this->connections[$origin]->requiresInterchangeWith($connection) ? $this->interchangeTimes[$origin] : 0;
            $canGetToThisConnection = array_key_exists($origin, $this->arrivals) && $departureTime >= $this->arrivals[$origin] + $interchangeTime;
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
            foreach ($this->nonTimetable[$origin] as $connection) {
                list($o, $destination, $duration) = $connection->toArray();
                $thisConnectionIsBetter = !array_key_exists($destination, $this->arrivals) || $this->arrivals[$destination] > $this->arrivals[$origin] + $duration;

                if ($thisConnectionIsBetter) {
                    $this->arrivals[$destination] = $this->arrivals[$origin] + $duration;
                    $this->connections[$destination] = $connection;
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
            $destination = $this->connections[$destination]->getOrigin();
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
