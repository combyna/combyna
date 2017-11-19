<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Loader;

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Config\Loader\Library\LibraryLoaderInterface;

/**
 * Class EnvironmentLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentLoader implements EnvironmentLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var LibraryLoaderInterface
     */
    private $libraryLoader;

    /**
     * @param ConfigParser $configParser
     * @param LibraryLoaderInterface $libraryLoader
     */
    public function __construct(
        ConfigParser $configParser,
        LibraryLoaderInterface $libraryLoader
    ) {
        $this->configParser = $configParser;
        $this->libraryLoader = $libraryLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadEnvironment(array $environmentConfig)
    {
        $libraryConfigs = $this->configParser->getOptionalElement(
            $environmentConfig,
            'libraries',
            'environment libraries',
            [],
            'array'
        );
        $libraryNodes = [];

        foreach ($libraryConfigs as $libraryConfig) {
            $libraryNodes[] = $this->libraryLoader->loadLibrary($libraryConfig);
        }

        return new EnvironmentNode($libraryNodes);
    }
}
