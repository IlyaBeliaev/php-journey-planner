<?php

use JourneyPlanner\Lib\Algorithm\SchedulePlanner;
use JourneyPlanner\Lib\Network\TimetableConnection;
use JourneyPlanner\Lib\Network\TransferPattern;

class SchedulePlannerTest extends PHPUnit_Framework_TestCase {

    public function testBasicJourney() {
        $transferPattern = new TransferPattern([
            [
                new TimetableConnection("A", "B", 1000, 1015, "LN1111"),
                new TimetableConnection("A", "B", 1020, 1045, "LN1112"),
                new TimetableConnection("A", "B", 1100, 1115, "LN1112"),
            ],
            [
                new TimetableConnection("B", "C", 1020, 1045, "LN1121"),
                new TimetableConnection("B", "C", 1100, 1145, "LN1122"),
                new TimetableConnection("B", "C", 1200, 1215, "LN1123"),
            ],
            [
                new TimetableConnection("C", "D", 1120, 1145, "LN1131"),
                new TimetableConnection("C", "D", 1200, 1245, "LN1132"),
                new TimetableConnection("C", "D", 1300, 1315, "LN1133"),
            ]
        ]);

        $scanner = new SchedulePlanner($transferPattern, [], []);
        $journeys = $scanner->getRoute("A", "D", 900);

        $this->assertEquals([
            [
                new TimetableConnection("A", "B", 1000, 1015, "LN1111"),
                new TimetableConnection("B", "C", 1020, 1045, "LN1121"),
                new TimetableConnection("C", "D", 1120, 1145, "LN1131"),
            ],
            [
                new TimetableConnection("A", "B", 1020, 1045, "LN1112"),
                new TimetableConnection("B", "C", 1100, 1145, "LN1122"),
                new TimetableConnection("C", "D", 1200, 1245, "LN1132"),
            ],
            [
                new TimetableConnection("A", "B", 1100, 1115, "LN1112"),
                new TimetableConnection("B", "C", 1200, 1215, "LN1123"),
                new TimetableConnection("C", "D", 1300, 1315, "LN1133"),
            ]
        ], $journeys);
    }

    public function testJourneyWithNonTimetableConnection() {

    }

    public function testJourneyWithUnreachableLegs() {

    }

}
