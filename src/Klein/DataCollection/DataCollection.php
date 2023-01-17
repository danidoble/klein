<?php
/**
 * Klein (klein.php) - A fast & flexible router for PHP
 *
 * @author      Chris O'Hara <cohara87@gmail.com>
 * @author      Trevor Suarez (Rican7) (contributor and v2 refactorer)
 * @copyright   (c) Chris O'Hara
 * @link        https://github.com/klein/klein.php
 * @license     MIT
 */

namespace Klein\DataCollection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * DataCollection
 *
 * A generic collection class to contain array-like data, specifically
 * designed to work with HTTP data (request params, session data, etc)
 *
 * Inspired by @fabpot's Symfony 2's HttpFoundation
 * @link https://github.com/symfony/HttpFoundation/blob/master/ParameterBag.php
 */
class DataCollection implements IteratorAggregate, ArrayAccess, Countable
{

    /**
     * Class properties
     */

    /**
     * Collection of data attributes
     *
     * @type array
     */
    protected array $attributes = array();


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param array $attributes The data attributes of this collection
     */
    public function __construct(array $attributes = array())
    {
        $this->attributes = $attributes;
    }

    /**
     * Returns all the key names in the collection
     *
     * If an optional mask array is passed, this only
     * returns the keys that match the mask
     *
     * @param array|null|string $mask The parameter mask array
     * @param boolean $fill_with_nulls Whether to fill the returned array with
     *  values to match the given mask, even if they don't exist in the collection
     * @return array
     */
    public function keys(array|null|string $mask = null, bool $fill_with_nulls = true): array
    {
        if (null !== $mask) {
            // Support a more "magical" call
            if (!is_array($mask)) {
                $mask = func_get_args();
            }

            /*
             * Make sure that the returned array has at least the values
             * passed into the mask, since the user will expect them to exist
             */
            if ($fill_with_nulls) {
                $keys = $mask;
            } else {
                $keys = array();
            }

            /*
             * Remove all the values from the keys
             * that aren't in the given mask
             */
            return array_intersect(
                array_keys($this->attributes),
                $mask
            ) + $keys;
        }

        return array_keys($this->attributes);
    }

    /**
     * Returns all the attributes in the collection
     *
     * If an optional mask array is passed, this only
     * returns the keys that match the mask
     *
     * @param array|null|string $mask The parameter mask array
     * @param boolean $fill_with_nulls Whether to fill the returned array with
     *  values to match the given mask, even if they don't exist in the collection
     * @return array
     */
    public function all(array|null|string $mask = null, bool $fill_with_nulls = true): array
    {
        if (null !== $mask) {
            // Support a more "magical" call
            if (!is_array($mask)) {
                $mask = func_get_args();
            }

            /*
             * Make sure that each key in the mask has at least a
             * null value, since the user will expect the key to exist
             */
            if ($fill_with_nulls) {
                $attributes = array_fill_keys($mask, null);
            } else {
                $attributes = array();
            }

            /*
             * Remove all the keys from the attributes
             * that aren't in the past mask
             */
            return array_intersect_key(
                $this->attributes,
                array_flip($mask)
            ) + $attributes;
        }

        return $this->attributes;
    }

    /**
     * Return an attribute of the collection
     *
     * Return a default value if the key doesn't exist
     *
     * @param string $key The name of the parameter to return
     * @param mixed|null $default_val The default value of the parameter if it contains no value
     * @return mixed
     */
    public function get(string $key, mixed $default_val = null): mixed
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return $default_val;
    }

    /**
     * Set an attribute of the collection
     *
     * @param string $key The name of the parameter to set
     * @param mixed $value The value of the parameter to set
     * @return DataCollection
     */
    public function set(string $key, mixed $value): DataCollection
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Replace the collection's attributes
     *
     * @param array $attributes The attributes to replace the collection's with
     * @return DataCollection
     */
    public function replace(array $attributes = array()): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Merge attributes with the collection's attributes
     *
     * Optionally allows a second boolean parameter to merge the attributes
     * into the collection in a "hard" manner, using the "array_replace"
     * method instead of the usual "array_merge" method
     *
     * @param array $attributes The attributes to merge into the collection
     * @param boolean $hard Whether to make the merge "hard"
     * @return DataCollection
     */
    public function merge(array $attributes = array(), bool $hard = false): static
    {
        // Don't waste our time with an "array_merge" call if the array is empty
        if (!empty($attributes)) {
            // Hard merge?
            if ($hard) {
                $this->attributes = array_replace(
                    $this->attributes,
                    $attributes
                );
            } else {
                $this->attributes = array_merge(
                    $this->attributes,
                    $attributes
                );
            }
        }

        return $this;
    }

    /**
     * See if an attribute exists in the collection
     *
     * @param string $key The name of the parameter
     * @return boolean
     */
    public function exists(string $key): bool
    {
        // Don't use "isset", since it returns false for null values
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Remove an attribute from the collection
     *
     * @param string $key The name of the parameter
     * @return void
     */
    public function remove(string $key): void
    {
        unset($this->attributes[$key]);
    }

    /**
     * Clear the collection's contents
     *
     * Semantic alias of a no-argument `$this->replace` call
     *
     * @return DataCollection
     */
    public function clear(): DataCollection|static
    {
        return $this->replace();
    }

    /**
     * Check if the collection is empty
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return empty($this->attributes);
    }

    /**
     * A quick convenience method to get an empty clone of the
     * collection. Great for dependency injection. :)
     *
     * @return DataCollection
     */
    public function cloneEmpty(): DataCollection
    {
        $clone = clone $this;
        $clone->clear();

        return $clone;
    }


    /*
     * Magic method implementations
     */

    /**
     * Magic "__get" method
     *
     * Allows the ability to arbitrarily request an attribute from
     * this instance while treating it as an instance property
     *
     * @param string $key The name of the parameter to return
     * @return mixed
     * @see get()
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Magic "__set" method
     *
     * Allows the ability to arbitrarily set an attribute from
     * this instance while treating it as an instance property
     *
     * @param string $key The name of the parameter to set
     * @param mixed $value The value of the parameter to set
     * @return void
     * @see set()
     */
    public function __set(string $key, mixed $value)
    {
        $this->set($key, $value);
    }

    /**
     * Magic "__isset" method
     *
     * Allows the ability to arbitrarily check the existence of an attribute
     * from this instance while treating it as an instance property
     *
     * @param string $key The name of the parameter
     * @return boolean
     * @see exists()
     */
    public function __isset(string $key)
    {
        return $this->exists($key);
    }

    /**
     * Magic "__unset" method
     *
     * Allows the ability to arbitrarily remove an attribute from
     * this instance while treating it as an instance property
     *
     * @param string $key The name of the parameter
     * @return void
     * @see remove()
     */
    public function __unset(string $key)
    {
        $this->remove($key);
    }


    /*
     * Interface required method implementations
     */

    /**
     * Get the aggregate iterator
     *
     * IteratorAggregate interface required method
     *
     * @return ArrayIterator
     * @see IteratorAggregate::getIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->attributes);
    }

    /**
     * Get an attribute via array syntax
     *
     * Allows the access of attributes of this instance while treating it like an array
     *
     * @param string $offset The name of the parameter to return
     * @return mixed
     * @see ArrayAccess::offsetGet
     * @see get()
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Set an attribute via array syntax
     *
     * Allows the access of attributes of this instance while treating it like an array
     *
     * @param string $offset The name of the parameter to set
     * @param mixed $value The value of the parameter to set
     * @return void
     * @see set()
     * @see ArrayAccess::offsetSet
     */
    public function offsetSet($offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Check existence an attribute via array syntax
     *
     * Allows the access of attributes of this instance while treating it like an array
     *
     * @param string $offset The name of the parameter
     * @return boolean
     * @see ArrayAccess::offsetExists
     * @see exists()
     */
    public function offsetExists($offset): bool
    {
        return $this->exists($offset);
    }

    /**
     * Remove an attribute via array syntax
     *
     * Allows the access of attributes of this instance while treating it like an array
     *
     * @param string $offset The name of the parameter
     * @return void
     * @see ArrayAccess::offsetUnset
     * @see remove()
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * Count the attributes via a simple "count" call
     *
     * Allows the use of the "count" function (or any internal counters)
     * to simply count the number of attributes in the collection.
     *
     * @return int
     * @see Countable::count
     */
    public function count(): int
    {
        return count($this->attributes);
    }
}
