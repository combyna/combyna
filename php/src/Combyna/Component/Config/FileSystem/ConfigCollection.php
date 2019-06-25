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
 * Class ConfigCollection
 *
 * A collection of Config objects
 *
 * @author Robin Cawser <robin.cawser@gmail.com>
 */
class ConfigCollection implements ConfigInterface
{
    /**
     * @var ConfigInterface[]
     */
    private $configs = [];

    /**
     * @param string $name
     * @param ConfigInterface $config
     */
    public function add($name, ConfigInterface $config)
    {
        $this->configs[$name] = $config;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $config = [];
        foreach ($this->configs as $key => $subConfig) {
            if ($subConfig instanceof Config) {
                $config = array_merge($config, $subConfig->toArray());
            }

            if ($subConfig instanceof ConfigCollection) {
                if (array_key_exists($key, $config)) {
                    $config[$key] = array_merge($config[$key], $subConfig->toArray());
                } else {
                    $config[$key] = $subConfig->toArray();
                }
            }
        }

        return $config;
    }
}
