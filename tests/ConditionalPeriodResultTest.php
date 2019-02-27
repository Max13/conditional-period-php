<?php

namespace MX\Tests;

use MX\ConditionalPeriod;
use MX\ConditionalType;
use DateInterval;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConditionalPeriodResultTest extends TestCase
{
    public function testFailConstructorWithInvalidResult()
    {
        $type = ConditionalType::DURATION;
        $lower = new DateInterval('P1D');
        $upper = new DateInterval('P2D');
        $exceptionMessage = 'The argument $result must be a valid DateInterval, an iso8601 interval specification string or a relative date string';
        $cp = null;

        try {
            new ConditionalPeriod($type, $lower, $upper, null);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            new ConditionalPeriod($type, $lower, $upper, -1);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            new ConditionalPeriod($type, $lower, $upper, 0);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            new ConditionalPeriod($type, $lower, $upper, 1);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            new ConditionalPeriod($type, $lower, $upper, 'FooBar');
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);
    }

    public function testConstructorWithValidUpper()
    {
        $type = ConditionalType::DURATION;
        $lower = new DateInterval('P1D');
        $upper = new DateInterval('P2D');

        // DateInterval
        $cp = new ConditionalPeriod($type, $lower, $upper, new DateInterval('P1D'));
        $this->assertEquals(new DateInterval('P1D'), $cp->result());

        $cp = new ConditionalPeriod($type, $lower, $upper, new DateInterval('P2D'));
        $this->assertEquals(new DateInterval('P2D'), $cp->result());

        $cp = new ConditionalPeriod($type, $lower, $upper, new DateInterval('P3D'));
        $this->assertEquals(new DateInterval('P3D'), $cp->result());

        $cp = new ConditionalPeriod($type, $lower, $upper, new DateInterval('P4D'));
        $this->assertEquals(new DateInterval('P4D'), $cp->result());

        // iso8601 interval
        $cp = new ConditionalPeriod($type, $lower, $upper, 'P1D');
        $this->assertEquals(new DateInterval('P1D'), $cp->result());

        $cp = new ConditionalPeriod($type, $lower, $upper, 'P2D');
        $this->assertEquals(new DateInterval('P2D'), $cp->result());

        $cp = new ConditionalPeriod($type, $lower, $upper, 'P3D');
        $this->assertEquals(new DateInterval('P3D'), $cp->result());

        $cp = new ConditionalPeriod($type, $lower, $upper, 'P4D');
        $this->assertEquals(new DateInterval('P4D'), $cp->result());

        // relative string
        $cp = new ConditionalPeriod($type, $lower, $upper, '+1 day');
        $this->assertEquals(new DateInterval('P1D'), $cp->result());

        $cp = new ConditionalPeriod($type, $lower, $upper, '+2 days');
        $this->assertEquals(new DateInterval('P2D'), $cp->result());

        $cp = new ConditionalPeriod($type, $lower, $upper, '+3 days');
        $this->assertEquals(new DateInterval('P3D'), $cp->result());

        $cp = new ConditionalPeriod($type, $lower, $upper, '+4 days');
        $this->assertEquals(new DateInterval('P4D'), $cp->result());
    }
}
