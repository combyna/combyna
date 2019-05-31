<?php

namespace Combyna\Component\Config\FileSystem;

use Combyna\Component\Config\YamlParser;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;

/**
 * Class YamlFileLoader
 * @package Combyna\Component\Config\Loader\FileSystem
 */
class YamlFileLoader extends FileLoader
{
    /**
     * @var YamlParser
     */
    private $yamlParser;

    public function __construct(FileLocatorInterface $locator, YamlParser $yamlParser)
    {
        parent::__construct($locator);
        $this->yamlParser = $yamlParser;
    }


    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        return new Config($this->yamlParser->parse(file_get_contents($resource)));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return \is_string($resource) && \in_array(pathinfo($resource, PATHINFO_EXTENSION), array('yml', 'yaml'), true) && (!$type || 'yaml' === $type);
    }
}
