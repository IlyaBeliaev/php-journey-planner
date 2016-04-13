<?php

namespace App;

use App\Command\Timetable;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class Console extends Application
{
    /**
     * CommandLine constructor.
     */
    public function __construct()
    {
        parent::__construct('PHP Journey Planner', '1.0');
    }

    /**
     * @return Command[]
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new Timetable();

        return $defaultCommands;
    }
}
