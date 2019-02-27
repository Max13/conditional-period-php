<?php

namespace MX;

use DateInterval;
use Exception;
use InvalidArgumentException;
use Serializable;

class ConditionalPeriod implements Serializable
{
    const CATEGORY = 'C';
    const DURATION = 'D';

    /**
     * Type of the condition
     *
     * @var string  One of the defined consts (1 char)
     */
    protected $type;

    /**
     * Lower boundary of the condition interval
     *
     * @var int|DateInteval
     */
    protected $lower;

    /**
     * Upper boundary of the condition interval
     *
     * The upper boundary ends just before its value "+1".
     * For instance, "to 6 days", means "to 7 days - 1 second"
     *
     * @var int|DateInterval
     */
    protected $upper;

    /**
     * Result of the condition
     *
     * @var DateInterval
     */
    protected $result;

    /**
     * Construct the ConditionalPeriod
     *
     * @param string                  $type   One of the ConditionalPeriod consts
     *                                        Also accepts ConditionalPeriod as string,
     *                                        when the constructor is used with 1 argument
     * @param int|string|DateInterval $lower  Lower boundary of the condition interval as:
     *                                        - int, for condition based on category
     *                                        - as DateInterval
     *                                        - as iso8601 interval specification
     *                                        - as relative date string
     * @param int|string|DateInterval $upper  Upper boundary of the condition interval as:
     *                                        see $lower, must be greater than or equal to $lower
     * @param string|DateInterval     $result Result of the condition as:
     *                                        see $lower, except int.
     */
    public function __construct($type, $lower = null, $upper = null, $result = null)
    {
        if (strlen($type) > 1) {
            try {
                list($type, $lower, $upper, $result) = $this->parseStringFormat($type);
            } catch (Exception $e) {
                throw $e;
            }
        }

        if (!in_array($type, [self::CATEGORY, self::DURATION], true)) {
            throw new InvalidArgumentException('The argument $type must be one of the ConditionalPeriod types (ConditionalPeriod::CATEGORY or ConditionalPeriod::DURATION). Input was: ('.gettype($type).')');
        }
        $this->type = $type;

        if ($type === self::CATEGORY && (gettype($lower) !== 'integer' || $lower < 1)) {
            throw new InvalidArgumentException('The argument $lower must be a valid category (Non null, positive integer). Input was: ('.gettype($lower).')');
        } elseif ($type === self::DURATION && !($lower instanceof DateInterval)) {
            if (is_string($lower)) {
                if ($lower[0] == 'P') {
                    $lower = new DateInterval($lower);
                } else {
                    $lower = DateInterval::createFromDateString($lower);
                }
            } else {
                throw new InvalidArgumentException('The argument $lower must be a valid DateInterval, an iso8601 interval specification string or a relative date string. Input was: ('.gettype($lower).')');
            }
        }
        $this->lower = $lower;

        if ($type === self::CATEGORY && (gettype($upper) !== 'integer' || $upper < 1)) {
            throw new InvalidArgumentException('The argument $upper must be a valid category (Non null, positive integer). Input was: ('.gettype($upper).')');
        } elseif ($type === self::DURATION && !($upper instanceof DateInterval)) {
            if (is_string($upper)) {
                if ($upper[0] == 'P') {
                    $upper = new DateInterval($upper);
                } else {
                    $upper = DateInterval::createFromDateString($upper);
                }
            } else {
                throw new InvalidArgumentException('The argument $upper must be a valid DateInterval, an iso8601 interval specification string or a relative date string. Input was: ('.gettype($upper).')');
            }
        }
        if (
            ($type === self::CATEGORY && $upper < $lower)
            || ($type === self::DURATION && date_create('2019-01-01')->add($upper) < date_create('2019-01-01')->add($lower))
        ) {
            throw new InvalidArgumentException('The argument $upper must be a greater than or equal to $lower). Input was: ('.gettype($upper).')');
        }
        $this->upper = $upper;

        if (is_string($result)) {
            if ($result[0] == 'P') {
                $result = new DateInterval($result);
            } else {
                $result = DateInterval::createFromDateString($result);
            }
        } elseif (!($result instanceof DateInterval)) {
            throw new InvalidArgumentException('The argument $result must be a valid DateInterval, an iso8601 interval specification string or a relative date string. Input was: ('.gettype($result).') '.$result);
        }
        $this->result = $result;
    }

    /**
     * Parse string format of this class
     *
     * @param  string $str String format of the class (got from toString() or __toString())
     * @return array       Array of arguments needed by the constructor
     *
     * @throw  InvalidArgumentException
     */
    protected function parseStringFormat($str)
    {
        $arguments = [
            $str[0],
        ];

        for ($i=0, $c=1; $i<2; ++$i) {
            if (($p = strpos($str, '-', $c + 1)) !== false) {
                $arg = substr($str, $c, $p - $c);
                $c = $p + 1;
            } elseif (($p = strpos($str, 'P', $c + 1)) !== false) {
                $arg = substr($str, $c, $p - $c);
                $c = $p;
            } else {
                throw new InvalidArgumentException("Invalid string format: Can't find argument #${i + 1}. Given: $str");
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
     * Type accessor
     *
     * @return string 1 char string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Lower boundary accessor
     * - int, if type is CATEGORY
     * - DateInterval, if type is DURATION
     *
     * @return int|DateInterval
     */
    public function lower()
    {
        return $this->lower;
    }

    /**
     * Upper boundary accessor
     * - int, if type is CATEGORY
     * - DateInterval, if type is DURATION
     *
     * @return int|DateInterval
     */
    public function upper()
    {
        return $this->upper;
    }

    /**
     * Result of the condition
     *
     * @return DateInterval
     */
    public function result()
    {
        return $this->result;
    }

    /**
     * Stringify a DateInterval as Iso8601 format
     *
     * @param  DateInterval $interval [description]
     * @return string
     *
     * @url http://php.net/manual/en/dateinterval.construct.php#119260
     */
    protected static function toIso8601(DateInterval $interval)
    {
        $date = null;
        if ($interval->y) {
            $date .= $interval->y . 'Y';
        }
        if ($interval->m) {
            $date .= $interval->m . 'M';
        }
        if ($interval->d) {
            $date .= $interval->d . 'D';
        }

        $time = null;
        if ($interval->h) {
            $time .= $interval->h . 'H';
        }
        if ($interval->i) {
            $time .= $interval->i . 'M';
        }
        if ($interval->s) {
            $time .= $interval->s . 'S';
        }
        if ($time) {
            $time = 'T' . $time;
        }

        $text = 'P' . $date . $time;
        if ($text == 'P') {
            return 'PT0S';
        }
        return $text;
    }

    /**
     * Stringify the object
     *
     * @return string
     */
    public function toString()
    {
        $iso8601 = "$this->type";

        if ($this->type === self::CATEGORY) {
            $iso8601 .= "$this->lower-$this->upper";
        } else {
            $iso8601 .= self::toIso8601($this->lower).self::toIso8601($this->upper);
        }

        $iso8601 .= self::toIso8601($this->result);

        return $iso8601;
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
        $this->__construct(unserialize($serialized));
    }
}
