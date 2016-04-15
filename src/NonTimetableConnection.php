<?php

namespace LJN;

class NonTimetableConnection extends Connection {

    private $duration;

    /**
     * @param string $origin
     * @param string $destination
     * @param int $duration
     * @param string $mode
     */
    public function __construct($origin, $destination, $duration, $mode = parent::WALK) {
        parent::__construct($origin, $destination, $mode);

        $this->duration = $duration;
    }

    public function getDuration() {
        return $this->duration;
    }

    public function toArray() {
        return [$this->origin, $this->destination, $this->duration];
    }

    public function requiresInterchangeWith(TimetableConnection $connection) {
        return false;
    }
}
