<?php

namespace LJN;

use \JsonSerializable;

abstract class Connection implements JsonSerializable {

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

    public function jsonSerialize() {
        return [
            "origin" => $this->origin,
            "destination" => $this->destination,
            "mode" => $this->mode
        ];
    }

    abstract function requiresInterchangeWith(TimetableConnection $connection);

}
