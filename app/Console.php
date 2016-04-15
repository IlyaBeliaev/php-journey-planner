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
     * @var App
     */
    private $app;

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        parent::__construct($app['name'], $app['version']);
    }

    /**
     * @return Command[]
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new Timetable($this->app->getDB());

        return $defaultCommands;
    }
}
