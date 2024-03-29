<?php

namespace MX;

use ArrayAccess;
use Countable;
use Carbon\CarbonInterval;
use InvalidArgumentException;
use JsonSerializable;
use Serializable;

/**
 * This class stores an array of ConditionalPeriod, allowing the user
 * to find() which ConditionalPeriod matches a given value.
 */
class ConditionalCollection implements ArrayAccess, Countable, JsonSerializable, Serializable
{
    /**
     * Array of MX\ConditionalPeriod
     *
     * @var array
     */
    protected $container = [];

    /**
     * Instanciate an MX\ConditionalCollection and sets its first element
     *
     * @param  MX\ConditionalPeriod|string $value The first value to store as:
     *                                                - MX\ConditionalPeriod
     *                                                - the string form of an MX\ConditionalPeriod
     * @return MX\ConditionalCollection
     *
     * @throws InvalidArgumentException
     */
    public static function create($value)
    {
        if (is_string($value)) {
            $value = ConditionalPeriod::parse($value);
        } elseif (!($value instanceof ConditionalPeriod)) {
            throw new InvalidArgumentException('Only MX\ConditionalPeriod (as object or string form) can be stored. Given: '.gettype($value));
        }

        $collection = new self;
        $collection[] = $value;
        return $collection;
    }

    /**
     * Instanciate an MX\ConditionalCollection from its string form
     *
     * @param  string $str
     * @return MX\ConditionalCollection
     *
     * @throws InvalidArgumentException
     */
    public static function parse($str)
    {
        if (!is_string($str)) {
            throw new InvalidArgumentException('First argument of parse() must be a string. '.gettype($str).' given.');
        }

        $collection = new self;

        foreach (explode(',', $str) as $periodStr) {
            $collection[] = ConditionalPeriod::parse($periodStr);
        }

        return $collection;
    }

    /**
     * Instanciate an MX\ConditionalCollection from an array of MX\ConditionalPeriod
     *
     * @param  array                    $array Array of ConditionalPeriod (Object or string)
     * @return MX\ConditionalCollection
     *
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $array)
    {
        $collection = new self;

        foreach ($array as $period) {
            if (is_string($period)) {
                $period = ConditionalPeriod::parse($period);
            }

            if (!($period instanceof ConditionalPeriod)) {
                throw new InvalidArgumentException('First argument of fromArray() must only contain only MX\ConditionalPeriod or its string form elements. '.gettype($period).' given.');
            }

            $collection[] = $period;
        }

        return $collection;
    }

    /**
     * Instanciate an MX\ConditionalCollection from an json string
     *
     * @param  string                   $json Json string
     * @return MX\ConditionalCollection
     *
     * @throws InvalidArgumentException
     */
    public static function fromJson($json)
    {
        return self::fromArray(json_decode($json ?: ''));
    }

    /**
     * Push a given MX\ConditionalPeriod in the container, at given index or last
     * and returns $this.
     *
     * @param  MX\ConditionalPeriod|string $value  Value to push as:
     *                                                 - as MX\ConditionalPeriod
     *                                                 - as string, used to construct a
     *                                                   MX\ConditionalPeriod
     * @param  int|null                    $offset Index, or null to push at the end
     *
     * @return MX\ConditionalCollection
     *
     * @throws InvalidArgumentException
     */
    public function push($value, $offset = null)
    {
        if (is_string($value)) {
            $value = ConditionalPeriod::parse($value);
        } elseif (!($value instanceof ConditionalPeriod)) {
            throw new InvalidArgumentException('Only MX\ConditionalPeriod (as object or string form) can be stored. Given: '.gettype($value));
        }

        if (count($this->container) && $value->type() !== $this->container[0]->type()) {
            throw new InvalidArgumentException('The MX\ConditionalPeriod set must have the same type as all the periods stored. Given: '.($value->type() === ConditionalType::CATEGORY ? 'ConditionalType::CATEGORY' : 'ConditionalType::DURATION'));
        }

        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            array_splice($this->container, $offset, 0, [$value]);
        }

        return $this;
    }

    /**
     * Find the MX\ConditionalPeriod matching the given value
     * and returns the matched one, or null if none matched.
     *
     * @param  Carbon\CarbonInterval|string|int $value Value to find as:
     *                                                     - as Carbon\CarbonInterval
     *                                                     - as string, used to construct a
     *                                                       Carbon\CarbonInterval
     *                                                     - int, for conditions based on category
     * @return MX\ConditionalPeriod|null
     *
     * @throws InvalidArgumentException
     */
    public function find($value)
    {
        if (is_string($value)) {
            $value = CarbonInterval::make($value);
        } elseif (!is_int($value) && !($value instanceof CarbonInterval)) {
            throw new InvalidArgumentException('Only Carbon\CarbonInterval (as object or string form) or integers can be found. Given: '.gettype($value));
        }

        foreach ($this->container as $period) {
            if ($period->match($value)) {
                return $period;
            }
        }

        return null;
    }

    /**
     * Arrayify this object
     *
     * @return array Array of ConditionalPeriod
     */
    public function toArray()
    {
        $array = [];

        foreach ($this->container as $period) {
            $array[] = $period->toString();
        }

        return $array;
    }

    /**
     * Json serialize this object
     *
     * @return string Json form of the ConditionalCollection
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Stringify this object
     *
     * @return string String form of ConditionalCollection
     */
    public function toString()
    {
        return implode(',', $this->container);
    }

    /**
     * Stringify this object
     *
     * @return string String form of ConditionalCollection
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @inherit
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * @inherit
     */
    public function offsetGet($offset) : mixed
    {
        return $this->container[$offset];
    }

    /**
     * @inherit
     */
    public function offsetSet($offset, $value) : void
    {
        $this->push($value, $offset);
    }

    /**
     * @inherit
     */
    public function offsetUnset($offset) : void
    {
        unset($this->container[$offset]);
    }

    /**
     * Internal container count
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->container);
    }

    /**
     * Expose the jsonable form of this object
     *
     * @return string
     */
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }

    /**
     * Serialize the object
     *
     * @return string
     *
     * @deprecated No longer used from PHP 8.1.0
     */
    public function serialize()
    {
        return serialize((string) $this);
    }

    /**
     * @inherits
     */
    public function __serialize() : array
    {
        return $this->toArray();
    }

    /**
     * Unserialize the object
     *
     * @param  string $serialized
     *
     * @deprecated No longer used from PHP 8.1.0
     */
    public function unserialize($serialized)
    {
        foreach (explode(',', unserialize($serialized)) as $periodStr) {
            $this->container[] = ConditionalPeriod::parse($periodStr);
        }
    }

    /**
     * @inherits
     */
    public function __unserialize(array $data): void
    {
        foreach ($data as $periodStr) {
            $this->container[] = ConditionalPeriod::parse($periodStr);
        }
    }
}
