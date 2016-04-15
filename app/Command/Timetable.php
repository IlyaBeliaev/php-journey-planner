<?php

namespace App\Command;

use Assertis\Util\Date;
use LJN\ConnectionListGenerator\TripFactory;
use LJN\ConnectionListGenerator\TripList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class Timetable extends Command
{
    const NAME = 'timetable';
    const DESCRIPTION = 'Generates a timetable for a specific date';

    const ARG_DATE_NAME = 'date';
    const ARG_DATE_DESCRIPTION = 'Date for which to generate a timetable';

    /**
     * @var TripFactory
     */
    private $tripFactory;

    /**
     * @param TripFactory $tripFactory
     */
    public function __construct(TripFactory $tripFactory)
    {
        parent::__construct();
        $this->tripFactory = $tripFactory;
    }

    /**
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION)
            ->addArgument(
                self::ARG_DATE_NAME,
                InputArgument::REQUIRED,
                self::ARG_DATE_DESCRIPTION
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = new Date($input->getArgument('date'));

        $start = $date->getDayEarlier();
        $end = $date->getDayLater();

        /** @var TripList $list */
        foreach ($this->tripFactory->getTripLists($start, $end) as $list) {
            foreach ($list->getConnections() as $line) {
                $output->writeln($line);
            }
        }

        return 0;
    }
}
