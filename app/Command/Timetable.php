<?php

namespace App\Command;

use Assertis\SimpleDatabase\SimpleDatabase;
use Assertis\Util\Date;
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
     * @var SimpleDatabase
     */
    private $db;

    /**
     * @param SimpleDatabase $db
     */
    public function __construct(SimpleDatabase $db)
    {
        parent::__construct();
        $this->db = $db;
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
        
        $output->writeln("Done");
        return 0;
    }
}
