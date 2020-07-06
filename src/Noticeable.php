<?php

namespace Rumur\WordPress\Notice;

abstract class Noticeable
{
    /**
     * Makes the notice content.
     *
     * @return string
     */
    abstract public function message(): string;
}
