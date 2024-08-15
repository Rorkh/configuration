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

        if (method_exists($instance, '__fill')) {
            call_user_func_array([$instance, '__fill'], []);
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

                $isPrefilled = isset($instance->$propertyName) && !$property->hasDefaultValue();

                if (!isset($array[$propertyName]) && $property->hasDefaultValue()) {
                    $fieldValue = $property->getDefaultValue();
                } else {
                    $fieldValue = $isPrefilled ? $instance->$propertyName : $array[$propertyName];
                }

                if (!isset($fieldValue)) {
                    throw new \RuntimeException("Missing mandatory field $propertyName for {$reflection->getName()}");
                }

                if (isset($configurationArguments['strict']) && $configurationArguments['strict']
                    && $reflectionType->getName() !== gettype($fieldValue)
                ) {
                    throw new \RuntimeException("$propertyName field value type not matches declared type");
                }

                $attributeInstance = $attribute->newInstance();
                if (method_exists($attributeInstance, '__fill')) {
                    $fieldValue = call_user_func_array([$attributeInstance, '__fill'], [$this, $fieldValue]);
                }

                $property->setAccessible(true);
                $property->setValue($instance, $fieldValue);
            }
        }

        return $instance;
    }
}