<?php

namespace MX;

use Carbon\CarbonInterval;
use Exception;
use InvalidArgumentException;
use Serializable;
use TypeError;

/**
 * This class is used to represent a business logic:
 * A result Carbon\CarbonInterval based on an interval condition:
 * - Between 2 categories (as int)
 * - Between 2 durations (as Carbon\CarbonInterval)
 */
class ConditionalPeriod implements Serializable
{
    /**
     * Type of the condition
     *
     * @var MX\ConditionalType
     */
    protected $type;

    /**
     * Lower boundary of the condition interval
     *
     * @var int|Carbon\CarbonInterval
     */
    protected $lower;

    /**
     * Upper boundary of the condition interval
     *
     * The upper boundary ends just before its value "+1".
     * For instance, "to 6 days", means "to 7 days - 1 second"
     *
     * @var int|Carbon\CarbonInterval
     */
    protected $upper;

    /**
     * Result of the condition
     *
     * @var Carbon\CarbonInterval
     */
    protected $result;

    /**
     * Check type value given, returns it transformed if needed
     *
     * @param  MX\ConditionalType $type
     * @return MX\ConditionalType
     *
     * @throws InvalidArgumentException
     */
    protected function checkTypeArgument($type)
    {
        if (!in_array($type, [ConditionalType::CATEGORY, ConditionalType::DURATION], true)) {
            throw new InvalidArgumentException('The argument $type must be one of the ConditionalPeriod types (ConditionalType::CATEGORY or ConditionalType::DURATION). Input was: ('.gettype($type).')');
        }

        return $type;
    }

    /**
     * Check lower value given, returns it transformed if needed
     *
     * @param  Carbon\CarbonInterval|string|int $lower  Lower boundary of the condition interval as:
     *                                                      - as Carbon\CarbonInterval
     *                                                      - as string, used to construct a
     *                                                        Carbon\CarbonInterval
     *                                                      - int, for conditions based on category
     * @return Carbon\CarbonInterval|int
     *
     * @throws InvalidArgumentException
     */
    protected function checkLowerArgument($lower)
    {
        if ($this->type === ConditionalType::CATEGORY) {
            if (!is_int($lower) || $lower <= 0) {
                throw new InvalidArgumentException('The argument $lower must be a valid category (Non null, positive integer). Input was: ('.gettype($lower).')');
            }
        } else {
            if (!($lower instanceof CarbonInterval) && !is_string($lower)) {
                throw new InvalidArgumentException('The argument $lower must be a valid Carbon\CarbonInterval, or a string used to construct an Carbon\CarbonInterval. Input was: ('.gettype($lower).')');
            }
            $lower = $lower instanceof CarbonInterval ? $lower : CarbonInterval::make($lower);
        }

        return $lower;
    }

    /**
     * Check upper value given, returns it transformed if needed
     *
     * @param  Carbon\CarbonInterval|string|int $upper  Upper boundary of the condition interval as:
     *                                                      - as Carbon\CarbonInterval
     *                                                      - as string, used to construct a
     *                                                        Carbon\CarbonInterval
     *                                                      - int, for conditions based on category
     * @return Carbon\CarbonInterval|int
     *
     * @throws InvalidArgumentException
     */
    protected function checkUpperArgument($upper)
    {
        if ($this->type === ConditionalType::CATEGORY) {
            if (!is_int($upper) || $upper < 0) {
                throw new InvalidArgumentException('The argument $upper must be a valid category (>= 0). Input was: ('.gettype($upper).')');
            }
        } else {
            if (!($upper instanceof CarbonInterval) && !is_string($upper)) {
                throw new InvalidArgumentException('The argument $upper must be a valid Carbon\CarbonInterval, or a string used to construct an Carbon\CarbonInterval. Input was: ('.gettype($upper).')');
            }
            $upper = $upper instanceof CarbonInterval ? $upper : CarbonInterval::make($upper);
        }

        if (
            (
                $this->type === ConditionalType::CATEGORY
                && $upper > 0
                && $upper < $this->lower
            )
            || (
                $this->type === ConditionalType::DURATION
                && $upper->totalSeconds > 0
                && $upper->compare($this->lower) < 0
            )
        ) {
            throw new InvalidArgumentException('The argument $upper must be a greater than or equal to $lower, or 0. $lower was ('.$this->lower.') and $upper was ('.$upper.')');
        }

        return $upper;
    }

    /**
     * Check result value given, returns it transformed if needed
     *
     * @param  Carbon\CarbonInterval|string $result  Result of the condition as:
     *                                                   - as Carbon\CarbonInterval
     *                                                   - as string, used to construct a
     *                                                     Carbon\CarbonInterval
     * @return Carbon\CarbonInterval
     *
     * @throws InvalidArgumentException
     */
    protected function checkResultArgument($result)
    {
        if (!($result instanceof CarbonInterval) && !is_string($result)) {
            throw new InvalidArgumentException('The argument $result must be a valid Carbon\CarbonInterval, or a string used to construct an Carbon\CarbonInterval. Input was: ('.gettype($result).')');
        }

        return $result instanceof CarbonInterval ? $result : CarbonInterval::make($result);
    }

    /**
     * Construct the ConditionalPeriod
     *
     * @param  MX\ConditionalType               $type   One of MX\ConditionalType consts
     * @param  Carbon\CarbonInterval|string|int $lower  Lower boundary of the condition interval as:
     *                                                      - as Carbon\CarbonInterval
     *                                                      - as string, used to construct a
     *                                                        Carbon\CarbonInterval
     *                                                      - int, for conditions based on category
     * @param  Carbon\CarbonInterval|string|int $upper  Upper boundary, included. Same as $lower
     * @param  Carbon\CarbonInterval|string     $result Result of the condition as:
     *                                                      - as Carbon\CarbonInterval
     *                                                      - as string, used to construct a
     *                                                        Carbon\CarbonInterval
     *
     * @throws InvalidArgumentException                 The argument couldn't be parsed
     */
    public function __construct($type, $lower, $upper, $result)
    {
        $this->type = $this->checkTypeArgument($type);
        $this->lower = $this->checkLowerArgument($lower);
        $this->upper = $this->checkUpperArgument($upper);
        $this->result = $this->checkResultArgument($result);
    }

    /**
     * Parse a string short format (given by toString() or __toString())
     * to an array
     *
     * @param  string $str
     * @return array       Array of arguments needed by the constructor
     */
    protected static function parseToArray($str)
    {
        if (!is_string($str)) {
            throw new TypeError('Argument 1 passed to MX\ConditionalPeriod::parseToArray() must be a string, '.gettype($str).' given.');
        }

        $arguments = [
            substr($str[0], 0, 1),
        ];

        for ($i=0, $c=1; $i<2; ++$i) {
            if (($p = strpos($str, '-', $c + 1)) !== false) {
                $arg = substr($str, $c, $p - $c);
                $c = $p + 1;
            } elseif (($p = strpos($str, 'P', $c + 1)) !== false) {
                $arg = substr($str, $c, $p - $c);
                $c = $p;
            } else {
                throw new InvalidArgumentException("Invalid string format: Can't find argument #".($i + 1).". Given: $str");
            }

            if (is_numeric($arg)) {
                $arg = intval($arg);
            }

            $arguments[] = $arg;
        }

        if ($c >= strlen($str)) {
            throw new InvalidArgumentException("Invalid string format: Can't find result. Given: $str");
        }
        $arguments[] = substr($str, $c);

        return $arguments;
    }

    /**
     * Parse string format of this class
     *
     * @param  string               $short_format String format of the class
     * @return MX\ConditionalPeriod
     *
     * @throws InvalidArgumentException
     */
    public static function parse($short_format)
    {
        $arguments = self::parseToArray($short_format);

        return new self(...$arguments);
    }

    /**
     * Type accessor
     *
     * @return MX\ConditionalType Which is a 1 letter string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Lower boundary accessor
     * - int, if type is CATEGORY
     * - Carbon\CarbonInterval, if type is DURATION
     *
     * @return int|Carbon\CarbonInterval
     */
    public function lower()
    {
        return $this->lower;
    }

    /**
     * Upper boundary accessor
     * - int, if type is CATEGORY
     * - Carbon\CarbonInterval, if type is DURATION
     *
     * @return int|Carbon\CarbonInterval
     */
    public function upper()
    {
        return $this->upper;
    }

    /**
     * Result of the condition
     *
     * @return Carbon\CarbonInterval
     */
    public function result()
    {
        return $this->result;
    }

    /**
     * Match the given value to the current condition
     *
     * @param  Carbon\CarbonInterval|string|int $value The value to match
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function match($value)
    {
        $value = $this->checkLowerArgument($value);

        if ($value instanceof CarbonInterval) {
            return $this->lower->compare($value) <= 0 && $this->upper->compare($value) >= 0;
        }

        return $value >= $this->lower && $value <= $this->upper;
    }

    /**
     * Stringify the object
     *
     * @return string
     */
    public function toString()
    {
        $str  = $this->type;
        $str .= $this->lower instanceof CarbonInterval ? $this->lower->spec() : $this->lower.'-';
        $str .= $this->upper instanceof CarbonInterval ? $this->upper->spec() : $this->upper;
        $str .= $this->result->spec();

        return $str;
    }

    /**
     * Echo the object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Serialize the object
     *
     * @return string
     */
    public function serialize()
    {
        return serialize((string) $this);
    }

    /**
     * Unserialize the object
     *
     * @param  string $serialized
     */
    public function unserialize($serialized)
    {
        $arguments = self::parseToArray(unserialize($serialized));

        $this->__construct(...$arguments);
    }
}
