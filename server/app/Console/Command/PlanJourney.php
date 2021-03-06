<?php

namespace JourneyPlanner\App\Console\Command;

use JourneyPlanner\Lib\Algorithm\Filter\SlowJourneyFilter;
use JourneyPlanner\Lib\Algorithm\MultiSchedulePlanner;
use JourneyPlanner\Lib\Network\Journey;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use JourneyPlanner\Lib\Storage\DatabaseLoader;
use JourneyPlanner\Lib\Algorithm\ConnectionScanner;

class PlanJourney extends ConsoleCommand {
    const NAME = 'plan-journey';
    const DESCRIPTION = 'Plan a journey';

    /**
     * @var DatabaseLoader
     */
    private $loader;

    /**
     * @param DatabaseLoader $loader
     */
    public function __construct(DatabaseLoader $loader) {
        parent::__construct();
        $this->loader = $loader;
    }

    /**
     * Set up arguments
     */
    protected function configure() {
        $this
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION)
            ->addArgument(
                'origin',
                InputArgument::REQUIRED,
                'Origin station CRS code e.g. CBW, HIB, LBG'
            )
            ->addArgument(
                'destination',
                InputArgument::REQUIRED,
                'Destination station CRS code e.g. CBW, HIB, LBG'
            )
            ->addArgument(
               'date',
               InputArgument::OPTIONAL,
               'Journey date e.g. 2016-06-20T07:40:00'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $date = $input->getArgument('date');
        if ($date) {
            $date = strtotime($date);
        } else {
            $date = time();
        }

        $this->planMutlipleJourneys($output, $input->getArgument('origin'), $input->getArgument('destination'), $date);
        //$this->planJourney($output, $input->getArgument('origin'), $input->getArgument('destination'), $date);

        return 0;
    }

    /**
     * @param  OutputInterface $out
     * @param  string          $origin
     * @param  string          $destination
     * @param  int             $targetTime
     */
    private function planJourney(OutputInterface $out, $origin, $destination, $targetTime) {
        $this->outputHeading($out, "Journey Planner");

        $timetableConnections = $this->outputTask($out, "Loading timetable", function () use ($targetTime, $origin) {
            return $this->loader->getUnprunedTimetableConnections($targetTime);
        });

        $nonTimetableConnections = $this->outputTask($out, "Loading non timetable connections", function () use ($targetTime) {
            return $this->loader->getNonTimetableConnections($targetTime);
        });

        $interchangeTimes = $this->outputTask($out, "Loading interchange", function () {
            return $this->loader->getInterchangeTimes();
        });

        $locations = $this->outputTask($out, "Loading locations", function () {
            return $this->loader->getLocations();
        });

        $scanner = new ConnectionScanner($timetableConnections, $nonTimetableConnections, $interchangeTimes);

        $route = $this->outputTask($out, "Plan journey", function () use ($scanner, $targetTime, $origin, $destination) {
            return $scanner->getJourneys($origin, $destination, strtotime('1970-01-01 '.date('H:i:s', $targetTime)));
        });

        $this->displayRoute($out, $locations, $route[0]);

        $this->outputMemoryUsage($out);
        $out->writeln("Connections: ".count($timetableConnections));
    }
    
    private function planMutlipleJourneys(OutputInterface $out, $origin, $destination, $targetTime) {
        $this->outputHeading($out, "Journey Planner");

        $schedules = $this->outputTask($out, "Loading schedules", function () use ($targetTime, $origin, $destination) {
            return $this->loader->getScheduleFromTransferPatternTimetable($origin, $destination, $targetTime);
        });

        $nonTimetableConnections = $this->outputTask($out, "Loading non timetable connections", function () use ($targetTime) {
            return $this->loader->getNonTimetableConnections($targetTime);
        });

        $interchangeTimes = $this->outputTask($out, "Loading interchange", function () {
            return $this->loader->getInterchangeTimes();
        });

        $locations = $this->outputTask($out, "Loading locations", function () {
            return $this->loader->getLocations();
        });

        $results = $this->outputTask($out, "Plan journeys", function () use ($schedules, $nonTimetableConnections, $interchangeTimes, $targetTime, $origin, $destination) {
            $time = strtotime('1970-01-01 '.date('H:i:s', $targetTime));
            $scanner = new MultiSchedulePlanner($schedules, $nonTimetableConnections, $interchangeTimes);
            $results = $scanner->getJourneys($origin, $destination, $time);
            $filter = new SlowJourneyFilter();

            return $filter->filter($results);
        });

        foreach ($results as $journey) {
            $this->displayRoute($out, $locations, $journey);
        }

        $this->outputMemoryUsage($out);
        $out->writeln("Number of transfer patterns: ".count($schedules));
    }

    /**
     * @param  OutputInterface $out
     * @param  array           $locations
     * @param  Journey         $journey
     */
    private function displayRoute(OutputInterface $out, array $locations, Journey $journey) {
        $this->outputHeading($out, "Route");

        foreach ($journey->getLegs() as $leg) {

            if (!$leg->isTransfer()) {
                foreach ($leg->getConnections() as $connection) {
                    $origin = sprintf('%-30s', $locations[$connection->getOrigin()]);
                    $destination = sprintf('%30s', $locations[$connection->getDestination()]);
                    $out->writeln(
                        date('H:i', $connection->getDepartureTime()).' '.$origin.' '.
                        sprintf('%-6s', $connection->getService()).' '.
                        $destination.' '.date('H:i', $connection->getArrivalTime())
                    );
                }
            }
            else {
                $origin = sprintf('%-30s', $locations[$leg->getOrigin()]);
                $destination = sprintf('%30s', $locations[$leg->getDestination()]);
                $out->writeln(
                    sprintf('%-6s', $leg->getMode()).
                    $origin.
                    '   to'.
                    $destination.
                    " (".($leg->getDuration() / 60)."mins)"
                );
            }
        }
    }
}
