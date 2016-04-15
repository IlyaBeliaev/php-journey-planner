<?php

namespace LJN\Tests\ConnectionListGenerator;

use Assertis\Ride\CallingPoint\CallingPoint;
use Assertis\Ride\CallingPoint\CallingPointList;
use Assertis\Ride\Location\LocationInterface;
use Assertis\Ride\Service\Service;
use Assertis\Ride\Service\ServiceInterface;
use Assertis\Util\Date;
use Assertis\Util\Time;
use LJN\ConnectionListGenerator\Trip;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class TripTest extends PHPUnit_Framework_TestCase
{
    private function getCallingPoint($stationNlc, $arrivalTime, $departureTime)
    {
        /** @var LocationInterface|PHPUnit_Framework_MockObject_MockObject $location */
        $location = $this->getMock(LocationInterface::class);
        $location->expects($this->any())->method('getNlc')->willReturn($stationNlc);

        $arrival = $arrivalTime ? Time::fromString($arrivalTime) : null;
        $departure = $departureTime ? Time::fromString($departureTime) : null;

        return new CallingPoint($location, $arrival, $departure, 0, 0);
    }

    /**
     * @test
     */
    public function getConnections()
    {
        $date = new Date('2016-04-20');
        $rsid = 'RSID';

        $one = $this->getCallingPoint('ABC', null, '10:05');
        $two = $this->getCallingPoint('DEF', '11:00', '11:05');
        $three = $this->getCallingPoint('GHI', '12:00', '12:05');
        $four = $this->getCallingPoint('JKL', '03:00', null);

        $callingPointList = new CallingPointList([$one, $two, $three, $four]);

        /** @var ServiceInterface|PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(ServiceInterface::class);
        $service->expects($this->any())->method('getCallingPoints')->willReturn($callingPointList);
        $service->expects($this->any())->method('getRsid')->willReturn($rsid);

        $trip = new Trip($service, $date);

        $expected = [
            strtotime('2016-04-20 10:05').','.strtotime('2016-04-20 11:00').',ABC,DEF,RSID',
            strtotime('2016-04-20 11:05').','.strtotime('2016-04-20 12:00').',DEF,GHI,RSID',
            strtotime('2016-04-20 12:05').','.strtotime('2016-04-21 03:00').',GHI,JKL,RSID',
        ];

        $this->assertSame($expected, $trip->getConnections());
    }
}
