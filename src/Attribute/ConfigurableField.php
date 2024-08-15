<?php

namespace Zeantar\Configuration\Attribute;

use Zeantar\Configuration\Factory\ConfigurationFactory;
use Zeantar\Configuration\Interface\ConfigurationInterface;

/**
 * Configuration configurable field attribute
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ConfigurableField extends Field
{
    /**
     * @param ConfigurationFactory $factory
     * @param mixed $fieldValue
     * 
     * @return ConfigurationInterface
     */
    public function __fill(ConfigurationFactory $factory, mixed $fieldValue): ConfigurationInterface
    {
        $instance = $factory->fromArray($fieldValue, $this->configuration);
        return $instance;
    }

    /**
     * @param string|null $name Field name
     * @param ConfigurationInterface $configuration;
     */
    public function __construct(private readonly ConfigurationInterface $configuration,
        private readonly ?string $name = null)
    {
        parent::__construct($name);
    }
}