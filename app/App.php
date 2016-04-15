<?php

namespace App;

use App\Command\Timetable;
use Assertis\Ride\CallingPoint\CallingPointFactory;
use Assertis\Ride\Service\ServiceFactory;
use Assertis\Ride\Station\StationFactory;
use Assertis\SimpleDatabase\SimpleDatabase;
use LJN\ConnectionListGenerator\TripFactory;
use Monolog\Logger;
use PDO;
use Pimple;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class App extends Pimple
{
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this['name'] = 'PHP Journey Planner';
        $this['version'] = '1.0';

        $this['logger'] = $this->share(function(App $app){
            return new Logger($app['name']);
        });

        $this['db'] = $this->share(function(){
            return new PDO('mysql:host=localhost;dbname=sardines', 'root', '');
        });

        $this['db.simple'] = $this->share(function(App $app){
            return new SimpleDatabase($app['db'], $app['logger']);
        });
        
        $this['console'] = $this->share(function(App $app){
            return new Console($app);
        });

        $this['factory.trip'] = $this->share(function(App $app){
            /** @var SimpleDatabase $db */
            $db = $app['db.simple'];
            $stationFactory = new StationFactory($db, $app['logger']);
            $callingPointFactory = new CallingPointFactory($db, $stationFactory);
            $serviceFactory = new ServiceFactory($db, $callingPointFactory);

            return new TripFactory($db, $serviceFactory);
        });

        $this['command.timetable'] = $this->share(function(App $app){
            return new Timetable($app['factory.trip']);
        });
    }

    /**
     * @return Console
     */
    public function getConsole()
    {
        return $this['console'];
    }

    /**
     * @return SimpleDatabase
     */
    public function getDB()
    {
        return $this['db.simple'];
    }
}
