<?php

namespace Combyna\Component\Config\FileSystem;

use Combyna\Component\Config\YamlParser;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;

/**
 * Class ApplicatonLoader
 * @package Combyna\Component\Config\Loader
 */
class ConfigLoader
{
    /**
     * @var YamlParser
     */
    private $yamlParser;

    /**
     * ConfigLoader constructor.
     * @param YamlParser $yamlParser
     */
    public function __construct(YamlParser $yamlParser)
    {
        $this->yamlParser = $yamlParser;
    }

    /**
     * {@inheritdoc}
     */
    public function load($path)
    {
        $locator = new FileLocator();
        $directoryLoader = new DirectoryLoader($locator);
        $loaderResolver = new LoaderResolver(array(
            new YamlFileLoader($locator, $this->yamlParser),
            $directoryLoader,
        ));
        $directoryLoader->setResolver($loaderResolver);

        $config = $directoryLoader->load($path, 'directory');

        return $config;
    }
}
