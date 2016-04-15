<?php

use LJN\ConnectionScanner;
use LJN\TimetableConnection;
use LJN\NonTimetableConnection;

class ConnectionScannerTest extends PHPUnit_Framework_TestCase {

    public function testBasicJourney() {
        $timetable = [
            new TimetableConnection("A", "B", 1000, 1015, "CS1234"),
            new TimetableConnection("B", "C", 1020, 1045, "CS1234"),
            new TimetableConnection("C", "D", 1100, 1115, "CS1234"),
        ];

        $scanner = new ConnectionScanner($timetable, [], []);
        $route = $scanner->getRoute("A", "D", 900);
        $this->assertEquals($timetable, $route);
    }

    public function testJourneyWithEarlyTermination() {
        $timetable = [
            new TimetableConnection("A", "B", 1000, 1015, "CS1234"),
            new TimetableConnection("B", "C", 1020, 1045, "CS1234"),
            new TimetableConnection("C", "D", 1100, 1115, "CS1234"),
            new TimetableConnection("D", "E", 1120, 1135, "CS1234"),
        ];

        $scanner = new ConnectionScanner($timetable, [], []);
        $route = $scanner->getRoute("A", "D", 900);
        $expectedRoute = [
            new TimetableConnection("A", "B", 1000, 1015, "CS1234"),
            new TimetableConnection("B", "C", 1020, 1045, "CS1234"),
            new TimetableConnection("C", "D", 1100, 1115, "CS1234"),
        ];

        $this->assertEquals($expectedRoute, $route);
    }

    public function testMultipleRoutes() {
        $timetable = [
            new TimetableConnection("A", "B", 1000, 1015, "CS1234"),
            new TimetableConnection("A", "C", 1005, 1025, "CS1234"),
            new TimetableConnection("B", "C", 1020, 1045, "CS1234"),
            new TimetableConnection("C", "D", 1030, 1100, "CS1234"),
            new TimetableConnection("C", "D", 1100, 1115, "CS1234"),
            new TimetableConnection("D", "E", 1105, 1125, "CS1234"),
            new TimetableConnection("D", "E", 1120, 1135, "CS1234"),
        ];

        $scanner = new ConnectionScanner($timetable, [], []);
        $route = $scanner->getRoute("A", "E", 900);
        $expectedRoute = [
            new TimetableConnection("A", "C", 1005, 1025, "CS1234"),
            new TimetableConnection("C", "D", 1030, 1100, "CS1234"),
            new TimetableConnection("D", "E", 1105, 1125, "CS1234"),
        ];

        $this->assertEquals($expectedRoute, $route);
    }

    public function testNoRoute() {
        $timetable = [
            new TimetableConnection("A", "B", 1000, 1015, "CS1234"),
            new TimetableConnection("C", "D", 1100, 1115, "CS1234"),
            new TimetableConnection("D", "E", 1105, 1125, "CS1234"),
            new TimetableConnection("D", "E", 1120, 1135, "CS1234"),
        ];

        $scanner = new ConnectionScanner($timetable, [], []);
        $route = $scanner->getRoute("A", "E", 900);
        $expectedRoute = [];

        $this->assertEquals($expectedRoute, $route);
    }

    public function testNoRouteBecauseOfMissedConnection() {
        $timetable = [
            new TimetableConnection("A", "B", 1000, 1015, "CS1234"),
            new TimetableConnection("B", "C", 1001, 1045, "CS1234"),
            new TimetableConnection("C", "D", 1100, 1115, "CS1234"),
            new TimetableConnection("D", "E", 1105, 1125, "CS1234"),
            new TimetableConnection("D", "E", 1120, 1135, "CS1234"),
        ];

        $scanner = new ConnectionScanner($timetable, [], []);
        $route = $scanner->getRoute("A", "E", 900);
        $expectedRoute = [];

        $this->assertEquals($expectedRoute, $route);
    }

    public function testRouteWithNonTimetabledConnection() {
        $timetable = [
            new TimetableConnection("A", "B", 1000, 1015, "CS1234"),
            new TimetableConnection("B", "C", 1020, 1045, "CS1234"),
            new TimetableConnection("C", "D", 1030, 1100, "CS1234"),
            new TimetableConnection("C", "D", 1100, 1115, "CS1234"),
        ];

        $nonTimetable = [
            "B" => [
                new NonTimetableConnection("B", "C", 5),
                new NonTimetableConnection("B", "E", 5),
            ]
        ];

        $scanner = new ConnectionScanner($timetable, $nonTimetable, []);
        $route = $scanner->getRoute("A", "D", 900);
        $expectedRoute = [
            new TimetableConnection("A", "B", 1000, 1015, "CS1234"),
            new NonTimetableConnection("B", "C", 5),
            new TimetableConnection("C", "D", 1030, 1100, "CS1234")
        ];

        $this->assertEquals($expectedRoute, $route);
    }

    public function testRouteWithNonTimetabledConnectionThatShouldntBeUsed() {
        $timetable = [
            new TimetableConnection("A", "B", 1000, 1015, "CS1234"),
            new TimetableConnection("B", "C", 1020, 1045, "CS1234"),
            new TimetableConnection("C", "D", 1030, 1100, "CS1234"),
            new TimetableConnection("C", "D", 1100, 1115, "CS1234"),
        ];

        $nonTimetable = [
            "B" => [
                new NonTimetableConnection("B", "C", 500),
                new NonTimetableConnection("B", "E", 5),
            ]
        ];

        $scanner = new ConnectionScanner($timetable, $nonTimetable, []);
        $route = $scanner->getRoute("A", "D", 900);
        $expectedRoute = [
            new TimetableConnection("A", "B", 1000, 1015, "CS1234"),
            new TimetableConnection("B", "C", 1020, 1045, "CS1234"),
            new TimetableConnection("C", "D", 1100, 1115, "CS1234")
        ];

        $this->assertEquals($expectedRoute, $route);
    }

    public function testRouteStartingInNonTimetabledConnection() {
        $timetable = [
            new TimetableConnection("A", "B", 1000, 1015, "CS1234"),
            new TimetableConnection("B", "C", 1020, 1045, "CS1234"),
            new TimetableConnection("C", "D", 1030, 1100, "CS1234"),
            new TimetableConnection("C", "D", 1100, 1115, "CS1234"),
        ];

        $nonTimetable = [
            "A" => [
                new NonTimetableConnection("A", "B", 5),
            ]
        ];

        $scanner = new ConnectionScanner($timetable, $nonTimetable, []);
        $route = $scanner->getRoute("A", "D", 900);
        $expectedRoute = [
            new NonTimetableConnection("A", "B", 5),
            new TimetableConnection("B", "C", 1020, 1045, "CS1234"),
            new TimetableConnection("C", "D", 1100, 1115, "CS1234"),
        ];

        $this->assertEquals($expectedRoute, $route);
    }

    public function testChangeOfService() {
        $timetable = [
            new TimetableConnection("A", "B", 1000, 1015, "CS1000"),
            new TimetableConnection("B", "C", 1020, 1045, "CS2000"),
            new TimetableConnection("C", "D", 1045, 1115, "CS2000"),
        ];

        $interchangeTimes = [
            "A" => 5,
            "B" => 5,
            "C" => 5
        ];

        $scanner = new ConnectionScanner($timetable, [], $interchangeTimes);
        $route = $scanner->getRoute("A", "D", 900);
        $this->assertEquals($timetable, $route);
    }

    public function testCantMakeConnectionBecauseOfInterchangeTime() {
        $timetable = [
            new TimetableConnection("A", "B", 1000, 1015, "CS1000"),
            new TimetableConnection("B", "C", 1020, 1045, "CS2000"),
            new TimetableConnection("C", "D", 1045, 1115, "CS2000"),
        ];

        $interchangeTimes = [
            "A" => 6,
            "B" => 6,
            "C" => 6
        ];

        $scanner = new ConnectionScanner($timetable, [], $interchangeTimes);
        $route = $scanner->getRoute("A", "D", 900);
        $this->assertEquals([], $route);
    }


}
