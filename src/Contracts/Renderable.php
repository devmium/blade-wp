<?php

namespace Devmium\Blade\Contracts;

/**
 * Interface Renderable
 * @package Devmium\Blade\Contracts
 */
interface Renderable
{
    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render();
}
