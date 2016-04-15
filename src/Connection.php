<?php

namespace LJN;

abstract class Connection {

    const TRAIN = "train", BUS = "bus", WALK = "walk", TUBE = "tube";

    protected $origin;
    protected $destination;
    protected $mode;

    /**
     * @param string $origin
     * @param string $destination
     * @param string $mode
     */
    public function __construct($origin, $destination, $mode = self::TRAIN) {
        $this->origin = $origin;
        $this->destination = $destination;
        $this->mode = $mode;
    }

    public function getOrigin() {
        return $this->origin;
    }

    public function getDestination() {
        return $this->destination;
    }

    public function getMode() {
        return $this->getMode();
    }

    public function toArray() {
        return [$this->origin, $this->destination, $this->mode];
    }

    abstract function requiresInterchangeWith(TimetableConnection $connection);

}
