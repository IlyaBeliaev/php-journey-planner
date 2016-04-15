<?php

namespace LJN\ConnectionListGenerator;

use Assertis\Util\ObjectList;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class TripList extends ObjectList
{
    /**
     * @param mixed $value
     * @return bool
     */
    public function accepts($value)
    {
        return $value instanceof Trip;
    }

    /**
     * @return array
     */
    public function getConnections()
    {
        return $this->reduce(function(array $out, Trip $trip){
            return array_merge($out, $trip->getConnections());
        }, []);
    }    
}
