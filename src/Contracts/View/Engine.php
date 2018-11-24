<?php

namespace Devmium\Blade\Contracts\View;

/**
 * Interface Engine
 * @package Devmium\Blade\Contracts\View
 */
interface Engine
{
    /**
     * Get the evaluated contents of the view.
     *
     * @param  string $path
     * @param  array $data
     *
     * @return string
     */
    public function get($path, array $data = []);
}
