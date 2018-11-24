<?php

namespace Devmium\Blade\Engines;

use Devmium\Blade\Contracts\View\Engine;

/**
 * Class FileEngine
 * @package Devmium\Blade\Engines
 */
class FileEngine implements Engine
{
    /**
     * Get the evaluated contents of the view.
     *
     * @param  string $path
     * @param  array $data
     *
     * @return string
     */
    public function get($path, array $data = [])
    {
        return file_get_contents($path);
    }
}
