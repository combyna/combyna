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

/**
 * Class ConfigLoader
 *
 * Loads configuration for a given path
 *
 * @author Robin Cawser <robin.cawser@gmail.com>
 */
class ConfigLoader
{
    /**
     * @var DirectoryLoader
     */
    private $directoryLoader;

    /**
     * @param DirectoryLoader $directoryLoader
     */
    public function __construct(DirectoryLoader $directoryLoader)
    {
        $this->directoryLoader = $directoryLoader;
    }

    /**
     * @param string $path
     * @return ConfigInterface
     */
    public function load($path)
    {
        $config = $this->directoryLoader->load($path, 'directory');

        return $config;
    }
}
