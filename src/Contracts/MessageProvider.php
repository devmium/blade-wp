<?php

namespace Devmium\Blade\Contracts;

/**
 * Interface MessageProvider
 * @package Devmium\Blade\Contracts
 */
interface MessageProvider
{
    /**
     * Get the messages for the instance.
     *
     * @return \Devmium\Blade\Contracts\MessageBag
     */
    public function getMessageBag();
}
