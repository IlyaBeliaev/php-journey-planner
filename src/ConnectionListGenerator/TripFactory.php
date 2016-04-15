<?php

namespace LJN\ConnectionListGenerator;

use Assertis\Ride\Service\Service;
use Assertis\Ride\Service\ServiceFactory;
use Assertis\SimpleDatabase\SimpleDatabase;
use Assertis\Util\Date;
use Generator;
use PDO;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class TripFactory
{
    /**
     * @var SimpleDatabase
     */
    private $db;
    /**
     * @var ServiceFactory
     */
    private $serviceFactory;

    /**
     * @param SimpleDatabase $db
     * @param ServiceFactory $serviceFactory
     */
    public function __construct(SimpleDatabase $db, ServiceFactory $serviceFactory)
    {
        $this->db = $db;
        $this->serviceFactory = $serviceFactory;
    }

    /**
     * @param Service $service
     * @param Date $start
     * @param Date $end
     * @return TripList
     */
    public function getTripList(Service $service, Date $start, Date $end)
    {
        $out = new TripList();

        $current = clone $start;

        while (!$current->isAfter($end)) {
            if ($service->getRunsOn()->matches($current)) {
                $out->append(new Trip($service, $current));
            }

            $current = $current->getDaysLater(1);
        }

        return $out;
    }

    /**
     * @param Service $s1
     * @param Service $s2
     * @return bool
     */
    private function serviceSame(Service $s1, Service $s2)
    {
        return
            $s1->getTuid() == $s2->getTuid() &&
            $s1->getStp() == $s2->getStp() &&
            $s1->getRunsFrom()->isSameDay($s2->getRunsFrom());
    }

    /**
     * @param Date $start
     * @param Date $end
     * @return Generator
     */
    public function getTripLists(Date $start, Date $end)
    {
        $statement = $this->db->executeQuery("
                SELECT tuid, runs_from 
                FROM service 
                WHERE runs_from <= :start AND runs_to >= :end 
                GROUP BY tuid, runs_from
            ",
            [
                'start' => $start,
                'end' => $end
            ]);

        /** @var Service $previous */
        $previous = null;

        while ($item = $statement->fetch(PDO::FETCH_ASSOC)) {

            $runsFrom = Date::fromString($item['runs_from']);
            $service = $this->serviceFactory->getService($item['tuid'], $runsFrom);

            if (!$service || ($previous && $this->serviceSame($previous, $service))) {
                continue;
            }

            yield $this->getTripList($service, $start, $end);

            $previous = $service;
        }
    }
}
