<?php

namespace Zeantar\Configuration\Factory;

use Zeantar\Configuration\Attribute\ConfigurableField;
use Zeantar\Configuration\Attribute\Configuration;
use Zeantar\Configuration\Attribute\Field;
use Zeantar\Configuration\ConfigurationEngine;
use Zeantar\Configuration\Interface\ConfigurationInterface;

class ConfigurationFactory
{
    public function __construct(private readonly ConfigurationEngine $engine)
    {
    }

    /**
     * @param string $json
     * @param ConfigurationInterface $configuration
     * 
     * @return void
     */
    public function fromArray(array $array, object|string $configurationClass): ConfigurationInterface
    {
        if (is_string($configurationClass)) {
            /** @var ConfigurationInterface */
            $instance = new $configurationClass;
        }

        if (is_object($configurationClass))
        {
            /** @var ConfigurationInterface */
            $instance = $configurationClass;
        }

        $reflection = new \ReflectionClass($configurationClass);

        $configurationArguments = [];

        foreach ($reflection->getAttributes() as $attribute) {
            if ($attribute->getName() !== Configuration::class) {
                continue;
            }

            $configurationArguments = $attribute->getArguments();
        }

        foreach ($reflection->getProperties() as $property) {
            $propertyName = $property->getName();
            $attributes = $property->getAttributes(Field::class, \ReflectionAttribute::IS_INSTANCEOF);

            foreach ($attributes as $attribute) {
                $arguments = $attribute->getArguments();

                if (isset($arguments['name'])) {
                    $propertyName = $arguments['name'];
                }

                $reflectionType = $property->getType();
                $nullable = $reflectionType->allowsNull();

                if (!$nullable && !isset($array[$propertyName])) {
                    throw new \RuntimeException("Missing mandatory field $propertyName for {$reflection->getName()}");
                }

                if (!isset($array[$propertyName])) {
                    $fieldValue = $property->getDefaultValue();
                } else {
                    $fieldValue = $array[$propertyName];
                }

                if (isset($configurationArguments['strict']) && $configurationArguments['strict']
                    && $reflectionType->getName() !== gettype($fieldValue)
                ) {
                    throw new \RuntimeException("$propertyName field value type not matches declared type");
                }

                if ($attribute->getName() == ConfigurableField::class) {
                    if (!isset($arguments['configuration'])) {
                        throw new \RuntimeException("Configuration not specified for field $propertyName");
                    }

                    $configurableInstance = $arguments['configuration'];
                    $this->fromArray($fieldValue, $configurableInstance);

                    $fieldValue = $configurableInstance;
                }

                $property->setAccessible(true);
                $property->setValue($instance, $fieldValue);
            }
        }

        return $instance;
    }
}