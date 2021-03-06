<?php

namespace JourneyPlanner\Lib\Network;

abstract class Connection {

    const TRAIN = "Train", BUS = "Bus", WALK = "Walk", TUBE = "Tube";

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

    /**
     * @return string
     */
    public function getOrigin() {
        return $this->origin;
    }

    /**
     * @return string
     */
    public function getDestination() {
        return $this->destination;
    }

    /**
     * @return string
     */
    public function getMode() {
        return $this->mode;
    }

    /**
     * @param  Connection $connection
     * @return boolean
     */
    abstract function requiresInterchangeWith(Connection $connection);

    /**
     * @return int
     */
    abstract function getDuration();
}
