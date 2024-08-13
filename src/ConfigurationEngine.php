<?php

namespace Zeantar\Configuration;

use Zeantar\Configuration\Extensions\CoreExtension;
use Zeantar\Configuration\Extension;
use Zeantar\Configuration\Interface\LoaderInterface;
use Zeantar\Configuration\Factory\ConfigurationFactory;
use Zeantar\Configuration\Interface\ConfigurationInterface;

class ConfigurationEngine
{
    /**
     * @var Extension[]
     */
    private array $extensions = [];

    /**
     * @var array<string, LoaderInterface>
     */
    private array $loaders;

    private ConfigurationFactory $factory;

    public function __construct()
    {
        $this->factory = new ConfigurationFactory($this);

        $this->addExtension(new CoreExtension);
    }

    /**
     * @param string $filepath
     * @param string $configuration
     * 
     * @return ConfigurationInterface
     */
    public function read(string $filepath, string $configuration): ConfigurationInterface
    {
        $extension = pathinfo($filepath, PATHINFO_EXTENSION);
        if (!array_key_exists($extension, $this->loaders)) {
            throw new \RuntimeException('Loader not found');
        }

        $loader = $this->loaders[$extension];

        $content = file_get_contents($filepath);
        return $this->factory->fromArray($loader->getData($content), $configuration);
    }

    public function addExtension(Extension $extension): void
    {
        $this->extensions[] = $extension;

        foreach ($extension->getLoaders() as $format => $loader) {
            $this->loaders[$format] = $loader;
        }
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function getFactory(): ConfigurationFactory
    {
        return $this->factory;
    }
}