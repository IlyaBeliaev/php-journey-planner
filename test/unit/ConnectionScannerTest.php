<?php

use LJN\ConnectionScanner;

class ConnectionScannerTest extends PHPUnit_Framework_TestCase {

    public function testBasicJourney() {
        $timetable = [
            ["A", "B", 1000, 1015],
            ["B", "C", 1020, 1045],
            ["C", "D", 1100, 1115],
        ];

        $scanner = new ConnectionScanner($timetable, []);
        $route = $scanner->getRoute("A", "D", 900);
        $this->assertEquals($timetable, $route);
    }

    public function testJourneyWithEarlyTermination() {
        $timetable = [
            ["A", "B", 1000, 1015],
            ["B", "C", 1020, 1045],
            ["C", "D", 1100, 1115],
            ["D", "E", 1120, 1135],
        ];

        $scanner = new ConnectionScanner($timetable, []);
        $route = $scanner->getRoute("A", "D", 900);
        $expectedRoute = [
            ["A", "B", 1000, 1015],
            ["B", "C", 1020, 1045],
            ["C", "D", 1100, 1115],
        ];

        $this->assertEquals($expectedRoute, $route);
    }

    public function testMultipleRoutes() {
        $timetable = [
            ["A", "B", 1000, 1015],
            ["A", "C", 1005, 1025],
            ["B", "C", 1020, 1045],
            ["C", "D", 1030, 1100],
            ["C", "D", 1100, 1115],
            ["D", "E", 1105, 1125],
            ["D", "E", 1120, 1135],
        ];

        $scanner = new ConnectionScanner($timetable, []);
        $route = $scanner->getRoute("A", "E", 900);
        $expectedRoute = [
            ["A", "C", 1005, 1025],
            ["C", "D", 1030, 1100],
            ["D", "E", 1105, 1125],
        ];

        $this->assertEquals($expectedRoute, $route);
    }

    public function testNoRoute() {
        $timetable = [
            ["A", "B", 1000, 1015],
            ["C", "D", 1100, 1115],
            ["D", "E", 1105, 1125],
            ["D", "E", 1120, 1135],
        ];

        $scanner = new ConnectionScanner($timetable, []);
        $route = $scanner->getRoute("A", "E", 900);
        $expectedRoute = [];

        $this->assertEquals($expectedRoute, $route);
    }

    public function testNoRouteBecauseOfMissedConnection() {
        $timetable = [
            ["A", "B", 1000, 1015],
            ["B", "C", 1001, 1045],
            ["C", "D", 1100, 1115],
            ["D", "E", 1105, 1125],
            ["D", "E", 1120, 1135],
        ];

        $scanner = new ConnectionScanner($timetable, []);
        $route = $scanner->getRoute("A", "E", 900);
        $expectedRoute = [];

        $this->assertEquals($expectedRoute, $route);
    }

    public function testRouteWithNonTimetabledConnection() {
        $timetable = [
            ["A", "B", 1000, 1015],
            ["B", "C", 1020, 1045],
            ["C", "D", 1030, 1100],
            ["C", "D", 1100, 1115],
        ];

        $nonTimetable = [
            "B" => [
                ["C", 5],
                ["E", 5]
            ]
        ];

        $scanner = new ConnectionScanner($timetable, $nonTimetable);
        $route = $scanner->getRoute("A", "D", 900);
        $expectedRoute = [
            ["A", "B", 1000, 1015],
            ["B", "C", 5],
            ["C", "D", 1030, 1100]
        ];

        $this->assertEquals($expectedRoute, $route);
    }

    public function testRouteWithNonTimetabledConnectionThatShouldntBeUsed() {
        $timetable = [
            ["A", "B", 1000, 1015],
            ["B", "C", 1020, 1045],
            ["C", "D", 1030, 1100],
            ["C", "D", 1100, 1115],
        ];

        $nonTimetable = [
            "B" => [
                ["C", 500],
                ["E", 5]
            ]
        ];

        $scanner = new ConnectionScanner($timetable, $nonTimetable);
        $route = $scanner->getRoute("A", "D", 900);
        $expectedRoute = [
            ["A", "B", 1000, 1015],
            ["B", "C", 1020, 1045],
            ["C", "D", 1100, 1115]
        ];

        $this->assertEquals($expectedRoute, $route);
    }

    public function testRouteStartingInNonTimetabledConnection() {
        $timetable = [
            ["A", "B", 1000, 1015],
            ["B", "C", 1020, 1045],
            ["C", "D", 1030, 1100],
            ["C", "D", 1100, 1115],
        ];

        $nonTimetable = [
            "A" => [
                ["B", 5]
            ]
        ];

        $scanner = new ConnectionScanner($timetable, $nonTimetable);
        $route = $scanner->getRoute("A", "D", 900);
        $expectedRoute = [
            ["A", "B", 5],
            ["B", "C", 1020, 1045],
            ["C", "D", 1100, 1115]
        ];

        $this->assertEquals($expectedRoute, $route);
    }


}
