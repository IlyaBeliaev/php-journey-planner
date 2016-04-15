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

    public function jsonSerialize() {
        return [
            "origin" => $this->origin,
            "destination" => $this->destination,
            "duration" => $this->duration,
            "mode" => $this->mode,
        ];
    }

    public function requiresInterchangeWith(TimetableConnection $connection) {
        return false;
    }
}
