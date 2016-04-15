<?php

namespace App;

use App\Command\Timetable;
use Assertis\SimpleDatabase\SimpleDatabase;
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

        $this['command.timetable'] = $this->share(function(App $app){
            return new Timetable($app['db.simple']);
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
