<?php

namespace Combyna\Component\Config\FileSystem;

/**
 * Class ConfigCollection
 * @package Combyna\Component\Config\Loader\FileSystem
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