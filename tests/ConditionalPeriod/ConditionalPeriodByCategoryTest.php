<?php

namespace MX\Tests\ConditionalPeriod;

use Carbon\CarbonInterval;
use Exception;
use InvalidArgumentException;
use MX\ConditionalPeriod;
use MX\ConditionalType;
use PHPUnit\Framework\TestCase;

class ConditionalPeriodByCategoryTest extends TestCase
{
    public function testConstructorWithValidType()
    {
        $cp = new ConditionalPeriod(ConditionalType::CATEGORY, 1, 2, new CarbonInterval('P1D'));
        $this->assertEquals(ConditionalType::CATEGORY, $cp->type());
    }

    public function testFailConstructorWithInvalidLower()
    {
        $type = ConditionalType::CATEGORY;
        $upper = 2;
        $result = new CarbonInterval('P1D');
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
            $cp = new ConditionalPeriod($type, new CarbonInterval('P1D'), $upper, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);
    }

    public function testConstructorWithValidLower()
    {
        $type = ConditionalType::CATEGORY;
        $result = new CarbonInterval('P1D');

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
        $type = ConditionalType::CATEGORY;
        $lower = 2;
        $result = new CarbonInterval('P1D');
        $exceptionMessage = 'The argument $upper must be a valid category (>= 0)';
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
            $cp = new ConditionalPeriod($type, $lower, new CarbonInterval('P1D'), $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod($type, $lower, 1, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith('The argument $upper must be a greater than or equal to $lower, or 0', $e->getMessage());
        }
        $this->assertNull($cp);
    }

    public function testConstructorWithValidUpper()
    {
        $type = ConditionalType::CATEGORY;
        $result = new CarbonInterval('P1D');

        $cp = new ConditionalPeriod($type, 1, 0, $result);
        $this->assertEquals(0, $cp->upper());

        $cp = new ConditionalPeriod($type, 1, 1, $result);
        $this->assertEquals(1, $cp->upper());

        $cp = new ConditionalPeriod($type, 1, 2, $result);
        $this->assertEquals(2, $cp->upper());

        $cp = new ConditionalPeriod($type, 2, 3, $result);
        $this->assertEquals(3, $cp->upper());

        $cp = new ConditionalPeriod($type, 2, 4, $result);
        $this->assertEquals(4, $cp->upper());
    }

    public function testDoesntMatchOutOfBoudariesValue()
    {
        $cp = new ConditionalPeriod(ConditionalType::CATEGORY, 3, 6, new CarbonInterval('P1D'));

        $this->assertFalse($cp->match(1));
        $this->assertFalse($cp->match(2));
        $this->assertFalse($cp->match(7));
        $this->assertFalse($cp->match(8));
    }

    public function testMatchInBoundariesValue()
    {
        $cp = new ConditionalPeriod(ConditionalType::CATEGORY, 3, 6, new CarbonInterval('P1D'));

        $this->assertTrue($cp->match(3));
        $this->assertTrue($cp->match(4));
        $this->assertTrue($cp->match(5));
        $this->assertTrue($cp->match(6));
    }
}
