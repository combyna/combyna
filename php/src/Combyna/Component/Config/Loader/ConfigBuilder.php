<?php

namespace Combyna\Component\Config\Loader;

class ConfigBuilder
{
    private $config = [];

    public function addConfig($key, array $config)
    {
        $this->config[$key][] = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }
}
