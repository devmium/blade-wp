<?php

namespace Devmium\Blade\Contracts;

/**
 * Interface Arrayable
 * @package Devmium\Blade\Contracts
 */
interface Arrayable
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray();
}
