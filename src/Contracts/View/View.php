<?php

namespace Devmium\Blade\Contracts\View;

use Devmium\Blade\Contracts\Renderable;

/**
 * Interface View
 * @package Devmium\Blade\Contracts\View
 */
interface View extends Renderable
{
    /**
     * Get the name of the view.
     *
     * @return string
     */
    public function name();

    /**
     * Add a piece of data to the view.
     *
     * @param  string|array $key
     * @param  mixed $value
     *
     * @return $this
     */
    public function with($key, $value = null);
}
