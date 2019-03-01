<?php

namespace MX\Tests;

use Carbon\CarbonInterval;
use Exception;
use InvalidArgumentException;
use MX\ConditionalPeriod;
use MX\ConditionalType;
use PHPUnit\Framework\TestCase;

class ConditionalPeriodByDurationTest extends TestCase
{
    public function testConstructorWithValidType()
    {
        $cp = new ConditionalPeriod(ConditionalType::DURATION, new CarbonInterval('P1D'), new CarbonInterval('P2D'), new CarbonInterval('P1D'));
        $this->assertEquals(ConditionalType::DURATION, $cp->type());
    }

    public function testFailConstructorWithInvalidLowerDuration()
    {
        $type = ConditionalType::DURATION;
        $upper = new CarbonInterval('P1D');
        $result = new CarbonInterval('P1D');
        $exceptionMessage = 'The argument $lower must be a valid Carbon\CarbonInterval, or a string used to construct an Carbon\CarbonInterval';
        $cp = null;

        try {
            new ConditionalPeriod($type, null, $upper, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            new ConditionalPeriod($type, -1, $upper, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            new ConditionalPeriod($type, 0, $upper, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);
    }

    public function testConstructorWithValidLower()
    {
        $type = ConditionalType::DURATION;
        $result = new CarbonInterval('P1D');

        // CarbonInterval
        $cp = new ConditionalPeriod($type, new CarbonInterval('P1D'), new CarbonInterval('P1D'), $result);
        $this->assertEquals(new CarbonInterval('P1D'), $cp->lower());

        $cp = new ConditionalPeriod($type, new CarbonInterval('P1D'), new CarbonInterval('P2D'), $result);
        $this->assertEquals(new CarbonInterval('P1D'), $cp->lower());

        $cp = new ConditionalPeriod($type, new CarbonInterval('P2D'), new CarbonInterval('P3D'), $result);
        $this->assertEquals(new CarbonInterval('P2D'), $cp->lower());

        $cp = new ConditionalPeriod($type, new CarbonInterval('P2D'), new CarbonInterval('P4D'), $result);
        $this->assertEquals(new CarbonInterval('P2D'), $cp->lower());

        // iso8601 interval
        $cp = new ConditionalPeriod($type, 'P1D', new CarbonInterval('P1D'), $result);
        $this->assertEquals(new CarbonInterval('P1D'), $cp->lower());

        $cp = new ConditionalPeriod($type, 'P1D', new CarbonInterval('P2D'), $result);
        $this->assertEquals(new CarbonInterval('P1D'), $cp->lower());

        $cp = new ConditionalPeriod($type, 'P2D', new CarbonInterval('P3D'), $result);
        $this->assertEquals(new CarbonInterval('P2D'), $cp->lower());

        $cp = new ConditionalPeriod($type, 'P2D', new CarbonInterval('P4D'), $result);
        $this->assertEquals(new CarbonInterval('P2D'), $cp->lower());

        // relative string
        $cp = new ConditionalPeriod($type, '1 day', new CarbonInterval('P1D'), $result);
        $this->assertEquals(new CarbonInterval('P1D'), $cp->lower());

        $cp = new ConditionalPeriod($type, '1 day', new CarbonInterval('P2D'), $result);
        $this->assertEquals(new CarbonInterval('P1D'), $cp->lower());

        $cp = new ConditionalPeriod($type, '2 days', new CarbonInterval('P3D'), $result);
        $this->assertEquals(new CarbonInterval('P2D'), $cp->lower());

        $cp = new ConditionalPeriod($type, '2 days', new CarbonInterval('P4D'), $result);
        $this->assertEquals(new CarbonInterval('P2D'), $cp->lower());
    }

    public function testFailConstructorWithInvalidUpper()
    {
        $type = ConditionalType::DURATION;
        $lower = new CarbonInterval('P2D');
        $result = new CarbonInterval('P1D');
        $exceptionMessage = 'The argument $upper must be a valid Carbon\CarbonInterval, or a string used to construct an Carbon\CarbonInterval';
        $cp = null;

        try {
            new ConditionalPeriod($type, $lower, null, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            new ConditionalPeriod($type, $lower, -1, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            new ConditionalPeriod($type, $lower, 0, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            new ConditionalPeriod($type, $lower, 1, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, $lower, new CarbonInterval('P1D'), $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith('The argument $upper must be a greater than or equal to $lower)', $e->getMessage());
        }
        $this->assertNull($cp);
    }

    public function testConstructorWithValidUpper()
    {
        $type = ConditionalType::DURATION;
        $result = new CarbonInterval('P1D');

        // CarbonInterval
        $cp = new ConditionalPeriod($type, new CarbonInterval('P1D'), new CarbonInterval('P1D'), $result);
        $this->assertEquals(new CarbonInterval('P1D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new CarbonInterval('P1D'), new CarbonInterval('P2D'), $result);
        $this->assertEquals(new CarbonInterval('P2D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new CarbonInterval('P2D'), new CarbonInterval('P3D'), $result);
        $this->assertEquals(new CarbonInterval('P3D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new CarbonInterval('P2D'), new CarbonInterval('P4D'), $result);
        $this->assertEquals(new CarbonInterval('P4D'), $cp->upper());

        // iso8601 interval
        $cp = new ConditionalPeriod($type, new CarbonInterval('P1D'), 'P1D', $result);
        $this->assertEquals(new CarbonInterval('P1D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new CarbonInterval('P1D'), 'P2D', $result);
        $this->assertEquals(new CarbonInterval('P2D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new CarbonInterval('P2D'), 'P3D', $result);
        $this->assertEquals(new CarbonInterval('P3D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new CarbonInterval('P2D'), 'P4D', $result);
        $this->assertEquals(new CarbonInterval('P4D'), $cp->upper());

        // relative string
        $cp = new ConditionalPeriod($type, new CarbonInterval('P1D'), '1 day', $result);
        $this->assertEquals(new CarbonInterval('P1D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new CarbonInterval('P1D'), '2 days', $result);
        $this->assertEquals(new CarbonInterval('P2D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new CarbonInterval('P2D'), '3 days', $result);
        $this->assertEquals(new CarbonInterval('P3D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new CarbonInterval('P2D'), '4 days', $result);
        $this->assertEquals(new CarbonInterval('P4D'), $cp->upper());
    }
}
