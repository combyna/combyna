<?php

namespace Combyna\Component\Config\Loader;

use Combyna\Component\Config\YamlParser;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;

class YamlFileLoader extends FileLoader
{
    /**
     * @var YamlParser
     */
    private $yamlParser;
    /**
     * @var ConfigBuilder
     */
    private $configBuilder;

    public function __construct(ConfigBuilder $configBuilder, YamlParser $yamlParser, FileLocatorInterface $locator)
    {
        parent::__construct($locator);
        $this->yamlParser = $yamlParser;
        $this->configBuilder = $configBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);

        $configKey = basename(pathinfo($path, PATHINFO_DIRNAME));
        $config = $this->yamlParser->parse(file_get_contents($path));

        $this->configBuilder->addConfig($configKey, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return \is_string($resource) && \in_array(pathinfo($resource, PATHINFO_EXTENSION), array('yml', 'yaml'), true);
    }
}