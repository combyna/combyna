<?php

namespace Combyna\Component\Config\FileSystem;

class Config implements ConfigInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function toArray()
    {
        return $this->config;
    }
}
