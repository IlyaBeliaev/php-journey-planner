<?php

namespace JourneyPlanner\Lib\Network;

/**
 * @author Linus Norton <linusnorton@gmail.com>
 */
class TransferPatternSchedule {

    /**
     * @var TransferPatternLeg[]
     */
    private $legs;

    /**
     * @param TransferPatternLeg[] $legs
     */
    public function __construct(array $legs) {
        $this->legs = $legs;
    }

    /**
     * @return TransferPatternLeg[]
     */
    public function getTransferLegs() {
        return $this->legs;
    }
    
}