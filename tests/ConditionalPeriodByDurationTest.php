<?php

namespace MX\Tests;

use MX\ConditionalPeriod;
use MX\ConditionalType;
use DateInterval;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConditionalPeriodByDurationTest extends TestCase
{
    public function testConstructorWithValidType()
    {
        $cp = new ConditionalPeriod(ConditionalType::DURATION, new DateInterval('P1D'), new DateInterval('P2D'), new DateInterval('P1D'));
        $this->assertEquals(ConditionalType::DURATION, $cp->type());
    }

    public function testFailConstructorWithInvalidLowerDuration()
    {
        $type = ConditionalType::DURATION;
        $upper = new DateInterval('P1D');
        $result = new DateInterval('P1D');
        $exceptionMessage = 'The argument $lower must be a valid DateInterval, an iso8601 interval specification string or a relative date string';
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
        $result = new DateInterval('P1D');

        // DateInterval
        $cp = new ConditionalPeriod($type, new DateInterval('P1D'), new DateInterval('P1D'), $result);
        $this->assertEquals(new DateInterval('P1D'), $cp->lower());

        $cp = new ConditionalPeriod($type, new DateInterval('P1D'), new DateInterval('P2D'), $result);
        $this->assertEquals(new DateInterval('P1D'), $cp->lower());

        $cp = new ConditionalPeriod($type, new DateInterval('P2D'), new DateInterval('P3D'), $result);
        $this->assertEquals(new DateInterval('P2D'), $cp->lower());

        $cp = new ConditionalPeriod($type, new DateInterval('P2D'), new DateInterval('P4D'), $result);
        $this->assertEquals(new DateInterval('P2D'), $cp->lower());

        // iso8601 interval
        $cp = new ConditionalPeriod($type, 'P1D', new DateInterval('P1D'), $result);
        $this->assertEquals(new DateInterval('P1D'), $cp->lower());

        $cp = new ConditionalPeriod($type, 'P1D', new DateInterval('P2D'), $result);
        $this->assertEquals(new DateInterval('P1D'), $cp->lower());

        $cp = new ConditionalPeriod($type, 'P2D', new DateInterval('P3D'), $result);
        $this->assertEquals(new DateInterval('P2D'), $cp->lower());

        $cp = new ConditionalPeriod($type, 'P2D', new DateInterval('P4D'), $result);
        $this->assertEquals(new DateInterval('P2D'), $cp->lower());

        // relative string
        $cp = new ConditionalPeriod($type, '+1 day', new DateInterval('P1D'), $result);
        $this->assertEquals(new DateInterval('P1D'), $cp->lower());

        $cp = new ConditionalPeriod($type, '+1 day', new DateInterval('P2D'), $result);
        $this->assertEquals(new DateInterval('P1D'), $cp->lower());

        $cp = new ConditionalPeriod($type, '+2 days', new DateInterval('P3D'), $result);
        $this->assertEquals(new DateInterval('P2D'), $cp->lower());

        $cp = new ConditionalPeriod($type, '+2 days', new DateInterval('P4D'), $result);
        $this->assertEquals(new DateInterval('P2D'), $cp->lower());
    }

    public function testFailConstructorWithInvalidUpper()
    {
        $type = ConditionalType::DURATION;
        $lower = new DateInterval('P2D');
        $result = new DateInterval('P1D');
        $exceptionMessage = 'The argument $upper must be a valid DateInterval, an iso8601 interval specification string or a relative date string';
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
            $cp = new ConditionalPeriod($type, $lower, new DateInterval('P1D'), $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith('The argument $upper must be a greater than or equal to $lower)', $e->getMessage());
        }
        $this->assertNull($cp);
    }

    public function testConstructorWithValidUpper()
    {
        $type = ConditionalType::DURATION;
        $result = new DateInterval('P1D');

        // DateInterval
        $cp = new ConditionalPeriod($type, new DateInterval('P1D'), new DateInterval('P1D'), $result);
        $this->assertEquals(new DateInterval('P1D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new DateInterval('P1D'), new DateInterval('P2D'), $result);
        $this->assertEquals(new DateInterval('P2D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new DateInterval('P2D'), new DateInterval('P3D'), $result);
        $this->assertEquals(new DateInterval('P3D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new DateInterval('P2D'), new DateInterval('P4D'), $result);
        $this->assertEquals(new DateInterval('P4D'), $cp->upper());

        // iso8601 interval
        $cp = new ConditionalPeriod($type, new DateInterval('P1D'), 'P1D', $result);
        $this->assertEquals(new DateInterval('P1D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new DateInterval('P1D'), 'P2D', $result);
        $this->assertEquals(new DateInterval('P2D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new DateInterval('P2D'), 'P3D', $result);
        $this->assertEquals(new DateInterval('P3D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new DateInterval('P2D'), 'P4D', $result);
        $this->assertEquals(new DateInterval('P4D'), $cp->upper());

        // relative string
        $cp = new ConditionalPeriod($type, new DateInterval('P1D'), '+1 day', $result);
        $this->assertEquals(new DateInterval('P1D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new DateInterval('P1D'), '+2 days', $result);
        $this->assertEquals(new DateInterval('P2D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new DateInterval('P2D'), '+3 days', $result);
        $this->assertEquals(new DateInterval('P3D'), $cp->upper());

        $cp = new ConditionalPeriod($type, new DateInterval('P2D'), '+4 days', $result);
        $this->assertEquals(new DateInterval('P4D'), $cp->upper());
    }
}
