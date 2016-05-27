<?php

namespace JourneyPlanner\App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use JourneyPlanner\Lib\Import\GTFSConverter;

class ImportFromGTFS extends Command
{
    const NAME = 'import-from-gtfs';
    const DESCRIPTION = 'Imports and converts data from the GTFS format';

    /**
     * @var GTFSConverter
     */
    private $converter;

    /**
     * @param GTFSConverter $converter
     */
    public function __construct(GTFSConverter $converter)
    {
        parent::__construct();
        $this->converter = $converter;
    }

    /**
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->converter->importTimetableConnections();

        return 0;
    }
}
