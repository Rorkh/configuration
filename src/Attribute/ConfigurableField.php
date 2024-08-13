<?php

namespace Zeantar\Configuration\Attribute;

use Zeantar\Configuration\Interface\ConfigurationInterface;

/**
 * Configuration configurable field attribute
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ConfigurableField extends Field
{
    /**
     * @param string|null $name Field name
     * @param ConfigurationInterface $configuration;
     */
    public function __construct(private readonly ?string $name = null,
        private readonly ConfigurationInterface $configuration)
    {
        parent::__construct($name);
    }
}