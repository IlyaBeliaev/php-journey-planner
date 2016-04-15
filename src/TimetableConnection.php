<?php

namespace LJN;

class TimetableConnection extends Connection {

    private $departureTime;
    private $arrivalTime;
    private $service;

    /**
     * @param string $origin
     * @param string $destination
     * @param int $departureTime
     * @param int $arrivalTime
     * @param string $service
     * @param string $mode
     */
    public function __construct($origin, $destination, $departureTime, $arrivalTime, $service, $mode = parent::TRAIN) {
        parent::__construct($origin, $destination, $mode);

        $this->departureTime = $departureTime;
        $this->arrivalTime = $arrivalTime;
        $this->service = $service;
    }

    public function getService() {
        return $this->service;
    }

    public function getDepartureTime() {
        return $this->departureTime;
    }

    public function getArrivalTime() {
        return $this->arrivalTime;
    }

    public function toArray() {
        return [$this->origin, $this->destination, $this->departureTime, $this->arrivalTime];
    }

    public function requiresInterchangeWith(TimetableConnection $connection) {
        return $this->service != $connection->getService();
    }

}
