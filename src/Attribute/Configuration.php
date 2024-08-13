<?php

namespace Zeantar\Configuration\Attribute;

/**
 * Configuration settings attribute
 */
class Configuration
{
    /**
     * @param boolean $strict
     */
    public function __construct(private readonly bool $strict = false)
    {}
}