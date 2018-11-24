<?php

namespace Devmium\Blade\Support;

use Countable;
use Devmium\Blade\Contracts\MessageBag as MessageBagContract;

/**
 * @mixin \Devmium\Blade\Contracts\MessageBag
 */
class ViewErrorBag implements Countable
{
    /**
     * The array of the view error bags.
     *
     * @var array
     */
    protected $bags = [];

    /**
     * Checks if a named MessageBag exists in the bags.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function hasBag($key = 'default')
    {
        return isset($this->bags[$key]);
    }

    /**
     * Get all the bags.
     *
     * @return array
     */
    public function getBags()
    {
        return $this->bags;
    }

    /**
     * Determine if the default message bag has any messages.
     *
     * @return bool
     */
    public function any()
    {
        return $this->count() > 0;
    }

    /**
     * Get the number of messages in the default bag.
     *
     * @return int
     */
    public function count()
    {
        return $this->getBag('default')->count();
    }

    /**
     * Get a MessageBag instance from the bags.
     *
     * @param  string $key
     *
     * @return \Devmium\Blade\Contracts\MessageBag
     */
    public function getBag($key)
    {
        return Arr::get($this->bags, $key) ?: new MessageBag;
    }

    /**
     * Dynamically call methods on the default bag.
     *
     * @param  string $method
     * @param  array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->getBag('default')->$method(...$parameters);
    }

    /**
     * Dynamically access a view error bag.
     *
     * @param  string $key
     *
     * @return \Devmium\Blade\Contracts\MessageBag
     */
    public function __get($key)
    {
        return $this->getBag($key);
    }

    /**
     * Dynamically set a view error bag.
     *
     * @param  string $key
     * @param  \Devmium\Blade\Contracts\MessageBag $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->put($key, $value);
    }

    /**
     * Add a new MessageBag instance to the bags.
     *
     * @param  string $key
     * @param  \Devmium\Blade\Contracts\MessageBag $bag
     *
     * @return $this
     */
    public function put($key, MessageBagContract $bag)
    {
        $this->bags[$key] = $bag;

        return $this;
    }

    /**
     * Convert the default bag to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getBag('default');
    }
}
