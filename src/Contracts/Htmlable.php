<?php

namespace Devmium\Blade\Contracts;

/**
 * Interface Htmlable
 * @package Devmium\Blade\Contracts
 */
interface Htmlable
{
    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml();
}
