<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Config\Loader;

use Combyna\Component\Common\Delegator\DelegatorInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Validator\Type\UnresolvedTypeDeterminer;
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
     * Adds a loader for a new kind of type
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
            try {
                $typeName = $this->configParser->getElement($config, 'type', 'type name');
            } catch (InvalidArgumentException $exception) {
                return new UnresolvedTypeDeterminer($exception->getMessage());
            }
        } else {
            // Type is just a string, the name of the type to load
            $typeName = $config;
            $config = [
                'type' => $typeName
            ];
        }

        if (strpos($typeName, '|') !== false) {
            // Type is the pipe shorthand for multiple
            $subTypeNames = explode('|', $typeName);
            $typeName = 'multiple';
            $config = [
                'type' => $typeName,
                'types' => $subTypeNames
            ];
        }

        if (!array_key_exists($typeName, $this->loaders)) {
            return new UnresolvedTypeDeterminer(sprintf(
                'No loader is registered for types of type "%s"',
                $typeName
            ));
        }

        return $this->loaders[$typeName]->load($config);
    }
}
