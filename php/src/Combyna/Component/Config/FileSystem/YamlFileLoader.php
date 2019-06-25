<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\FileSystem;

use Combyna\Component\Config\YamlParser;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;

/**
 * Class YamlFileLoader
 *
 * Loads the configuration from a Yaml file
 *
 * @author Robin Cawser <robin.cawser@gmail.com>
 */
class YamlFileLoader extends FileLoader
{
    /**
     * @var YamlParser
     */
    private $yamlParser;

    /**
     * @param FileLocatorInterface $locator
     * @param YamlParser $yamlParser
     */
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
