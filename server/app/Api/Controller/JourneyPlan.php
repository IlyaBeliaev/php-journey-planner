<?php

namespace JourneyPlanner\App\Api\Controller;

use JourneyPlanner\Lib\Algorithm\Filter\SlowJourneyFilter;
use JourneyPlanner\Lib\Algorithm\MultiSchedulePlanner;
use JourneyPlanner\Lib\Network\Journey;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JourneyPlanner\App\Api\View\JourneyView;

class JourneyPlan {

    /**
     * @param Application $app
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Application $app, Request $request) {
        $origin = $request->get('origin');
        $destination = $request->get('destination');
        $targetTime = strtotime($request->get('date'));

        $schedules = $app['loader.database']->getScheduleFromTransferPatternTimetable($origin, $destination, $targetTime);
        $nonTimetableConnections = $app['loader.database']->getNonTimetableConnections($targetTime);
        $interchangeTimes = $app['loader.database']->getInterchangeTimes();

        $scanner = new MultiSchedulePlanner($schedules, $nonTimetableConnections, $interchangeTimes);
        $filter = new SlowJourneyFilter();
        $results = $scanner->getJourneys($origin, $destination, strtotime('1970-01-01 '.date('H:i:s', $targetTime)));
        $journeys = $filter->filter($results);

        $views = array_map(function(Journey $journey) { return new JourneyView($journey); }, $journeys);

        return new JsonResponse($views);
    }
}
