<?php

namespace MX\Tests;

use MX\ConditionalPeriod;
use MX\ConditionalType;
use DateInterval;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConditionalPeriodTypeTest extends TestCase
{
    public function testFailConstructor()
    {
        $exceptionMessage = 'The argument $type must be one of the ConditionalPeriod types';
        $result = new DateInterval('P1D');
        $cp = null;

        try {
            $cp = new ConditionalPeriod(null, 1, 2, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod(0, 1, 2, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod(1, 1, 2, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod('a', 1, 2, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);

        try {
            $cp = new ConditionalPeriod('c', 1, 2, $result);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertStringStartsWith($exceptionMessage, $e->getMessage());
        }
        $this->assertNull($cp);
    }
}
