<?php

namespace LJN\ConnectionListGenerator;

use Assertis\Ride\CallingPoint\CallingPoint;
use Assertis\Ride\Service\Service;
use Assertis\Ride\Service\ServiceInterface;
use Assertis\Util\Date;
use Assertis\Util\Time;
use DateTime;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class Trip
{
    /**
     * @var ServiceInterface
     */
    private $service;
    /**
     * @var Date
     */
    private $date;

    /**
     * @param ServiceInterface $service
     * @param Date $date
     */
    public function __construct(ServiceInterface $service, Date $date)
    {
        $this->service = $service;
        $this->date = $date;
    }

    /**
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return Date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param Time $time
     * @param $isNextDay
     * @return int
     */
    private function getTimestamp(Time $time, $isNextDay)
    {
        $current = new DateTime(
            $isNextDay ?
                $this->date->getDayLater()->formatShort() :
                $this->date->formatShort()
        );

        $current->setTime($time->getHours(), $time->getMinutes());

        return $current->getTimestamp();
    }

    public function getConnections()
    {
        $out = [];

        $originalDepartureTime = null;
        $departures = [];

        /** @var CallingPoint $current */
        foreach ($this->service->getCallingPoints() as $current) {

            if (!$originalDepartureTime) {
                $originalDepartureTime = $current->getDepartTime();
            }

            if ($current->getArriveTime() && $departures) {
                /** @var CallingPoint $departure */
                foreach ($departures as $departure) {
                    $origin = $departure->getLocation()->getNlc();
                    $destination = $current->getLocation()->getNlc();

                    $departure = $this->getTimestamp(
                        $departure->getDepartTime(),
                        $departure->getDepartTime()->isBefore($originalDepartureTime)
                    );

                    $arrival = $this->getTimestamp(
                        $current->getArriveTime(),
                        $current->getArriveTime()->isBefore($originalDepartureTime)
                    );

                    $rsid = $this->service->getRsid();

                    $out[] = "{$departure},{$arrival},{$origin},{$destination},{$rsid}";
                }

                if ($current->getDepartTime()) {
                    $departures = [];
                }
            }

            if ($current->getDepartTime()) {
                $departures[] = $current;
            }
        }

        return $out;
    }
}
