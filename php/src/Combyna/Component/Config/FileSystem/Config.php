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
 * Class Config
 *
 * Encapsulates configuration loaded from a file
 *
 * @author Robin Cawser <robin.cawser@gmail.com>
 */
class Config implements ConfigInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->config;
    }
}
