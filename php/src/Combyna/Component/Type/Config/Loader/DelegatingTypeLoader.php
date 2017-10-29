<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Config\Loader;

use Combyna\Component\Common\DelegatorInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use InvalidArgumentException;

/**
 * Class DelegatingTypeLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingTypeLoader implements TypeLoaderInterface, DelegatorInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var TypeTypeLoaderInterface[]
     */
    private $loaders = [];

    /**
     * @param ConfigParser $configParser
     */
    public function __construct(ConfigParser $configParser)
    {
        $this->configParser = $configParser;
    }

    /**
     * Adds a loader for a new type of type
     *
     * @param TypeTypeLoaderInterface $typeTypeLoader
     */
    public function addLoader(TypeTypeLoaderInterface $typeTypeLoader)
    {
        foreach ($typeTypeLoader->getTypes() as $type) {
            $this->loaders[$type] = $typeTypeLoader;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        if (is_array($config)) {
            $type = $this->configParser->getElement($config, 'type', 'type name');
        } else {
            $type = $config;
            $config = [
                'type' => $config
            ];
        }

        if (!array_key_exists($type, $this->loaders)) {
            throw new InvalidArgumentException(
                'No loader is registered for types of type "' . $type . '"'
            );
        }

        return $this->loaders[$type]->load($config);
    }
}
