<?php

namespace MX\Tests;

use MX\ConditionalPeriod;
use DateInterval;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConditionalPeriodByCategoryTest extends TestCase
{
    public function testConstructorWithValidType()
    {
        $cp = new ConditionalPeriod(ConditionalPeriod::CATEGORY, 1, 2, new DateInterval('P1D'));
        $this->assertEquals(ConditionalPeriod::CATEGORY, $cp->type());
    }

    public function testFailConstructorWithInvalidLower()
    {
        $type = ConditionalPeriod::CATEGORY;
        $upper = 2;
        $result = new DateInterval('P1D');
        $exceptionMessage = 'The argument $lower must be a valid category (Non null, positive integer)';
        $cp = null;

        try {
            $cp = new ConditionalPeriod($type, null, $upper, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, -1, $upper, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, 0, $upper, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, 'FooBar', $upper, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, '', $upper, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, '+1 day', $upper, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, new DateInterval('P1D'), $upper, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);
    }

    public function testConstructorWithValidLower()
    {
        $type = ConditionalPeriod::CATEGORY;
        $result = new DateInterval('P1D');

        $cp = new ConditionalPeriod($type, 1, 1, $result);
        $this->assertEquals(1, $cp->lower());

        $cp = new ConditionalPeriod($type, 1, 2, $result);
        $this->assertEquals(1, $cp->lower());

        $cp = new ConditionalPeriod($type, 2, 3, $result);
        $this->assertEquals(2, $cp->lower());

        $cp = new ConditionalPeriod($type, 2, 4, $result);
        $this->assertEquals(2, $cp->lower());
    }

    public function testFailConstructorWithInvalidUpper()
    {
        $type = ConditionalPeriod::CATEGORY;
        $lower = 2;
        $result = new DateInterval('P1D');
        $exceptionMessage = 'The argument $upper must be a valid category (Non null, positive integer)';
        $cp = null;

        try {
            $cp = new ConditionalPeriod($type, $lower, null, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, $lower, -1, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, $lower, 0, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, $lower, 'FooBar', $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, $lower, '', $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, $lower, new DateInterval('P1D'), $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, $lower, 1, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith('The argument $upper must be a greater than or equal to $lower)', $e->getMessage());
        }
        $this->assertNull($cp);
    }

    public function testConstructorWithValidUpper()
    {
        $type = ConditionalPeriod::CATEGORY;
        $result = new DateInterval('P1D');

        $cp = new ConditionalPeriod($type, 1, 1, $result);
        $this->assertEquals(1, $cp->upper());

        $cp = new ConditionalPeriod($type, 1, 2, $result);
        $this->assertEquals(2, $cp->upper());

        $cp = new ConditionalPeriod($type, 2, 3, $result);
        $this->assertEquals(3, $cp->upper());

        $cp = new ConditionalPeriod($type, 2, 4, $result);
        $this->assertEquals(4, $cp->upper());
    }
}
