<?php

namespace MX\Tests\ConditionalPeriod;

use Carbon\CarbonInterval;
use Exception;
use InvalidArgumentException;
use MX\ConditionalPeriod;
use MX\ConditionalType;
use PHPUnit\Framework\TestCase;

class ConditionalPeriodFromToStringTest extends TestCase
{
    public function testCategoryFromToString()
    {
        $cpString = 'C1-2P1Y';
        $cp = new ConditionalPeriod(ConditionalType::CATEGORY, 1, 2, new CarbonInterval('P1Y'));

        $this->assertEquals($cpString, $cp->toString());
        $this->assertEquals($cp, ConditionalPeriod::parse($cpString));
    }

    public function testCategoryFromToSerialization()
    {
        $cpString = 'O:20:"MX\ConditionalPeriod":4:{i:0;s:1:"C";i:1;i:1;i:2;i:2;i:3;s:3:"P1Y";}';
        $cp = new ConditionalPeriod(ConditionalType::CATEGORY, 1, 2, new CarbonInterval('P1Y'));

        $this->assertEquals($cpString, serialize($cp));
        $this->assertEquals($cp, unserialize($cpString));
    }

    public function testDurationFromToString()
    {
        $cpString = 'DP1YP1Y2M3DP1Y2M3DT1H2M3S';
        $cp = new ConditionalPeriod(ConditionalType::DURATION, new CarbonInterval('P1Y'), new CarbonInterval('P1Y2M3D'), new CarbonInterval('P1Y2M3DT1H2M3S'));

        $this->assertEquals($cpString, $cp->toString());
        $this->assertEquals($cp, ConditionalPeriod::parse($cpString));
    }

    public function testDurationFromToSerialization()
    {
        $cpString = 'O:20:"MX\ConditionalPeriod":4:{i:0;s:1:"D";i:1;s:3:"P1Y";i:2;s:7:"P1Y2M3D";i:3;s:14:"P1Y2M3DT1H2M3S";}';
        $cp = new ConditionalPeriod(ConditionalType::DURATION, new CarbonInterval('P1Y'), new CarbonInterval('P1Y2M3D'), new CarbonInterval('P1Y2M3DT1H2M3S'));

        $this->assertEquals($cpString, serialize($cp));
        $this->assertEquals($cp, unserialize($cpString));
    }
}
