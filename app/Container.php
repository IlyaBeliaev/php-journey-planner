<?php

namespace JourneyPlanner\App;

use JourneyPlanner\App\Command\ImportFromGTFS;
use JourneyPlanner\Lib\Import\GTFSConverter;
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

        $this['command.import_from_gtfs'] = function(Container $container) {
            return new ImportFromGTFS($container['import.gtfs_converter']);
        };

        $this['import.gtfs_converter'] = function(Container $container) {
            return new GTFSConverter($container['db'], $container['logger']);
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
