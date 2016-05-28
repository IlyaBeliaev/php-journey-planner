<?php

namespace JourneyPlanner\App;

use JourneyPlanner\App\Command\PlanJourney;
use JourneyPlanner\Lib\DatabaseLoader;
use PDO;
use Pimple\Container;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Container extends Container {

    /**
     * @param array $values
     */
    public function __construct(array $values = []) {
        parent::__construct($values);

        $this['name'] = 'PHP Journey Planner';
        $this['version'] = '1.1';

        $this['console'] = function(Container $container) {
            return new Console($container);
        };

        $this['db'] = function() {
            return new PDO('mysql:host=localhost;dbname=ojp', 'root', '');
        };

        $this['command.plan_journey'] = function(Container $container) {
            return new PlanJourney($container['loader.database']);
        };

        $this['loader.database'] = function(Container $container) {
            return new DatabaseLoader($container['db']);
        };

        $this['logger'] = function() {
            $stream = new StreamHandler('php://stdout');
            $logger = new Logger('php-journey-planner');
            $logger->pushHandler($stream);

            return $logger;
        };
    }

    /**
     * @return Console
     */
    public function getConsole() {
        return $this['console'];
    }
}
