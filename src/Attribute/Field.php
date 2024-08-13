<?php

namespace Zeantar\Configuration\Attribute;

use Zeantar\Configuration\Interface\FieldMiddlewareInterface;

/**
 * Configuration field attribute
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Field
{
    /**
     * @param string|null $name Field name
     */
    public function __construct(private readonly ?string $name = null)
    {
    }
}