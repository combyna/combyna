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

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Type\StaticListType;

/**
 * Class StaticListTypeLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticListTypeLoader implements TypeTypeLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var TypeLoaderInterface
     */
    private $typeLoader;

    /**
     * @param ConfigParser $configParser
     * @param TypeLoaderInterface $typeLoader
     */
    public function __construct(ConfigParser $configParser, TypeLoaderInterface $typeLoader)
    {
        $this->configParser = $configParser;
        $this->typeLoader = $typeLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        $elementTypeConfig = $this->configParser->getElement($config, 'element', 'type for elements');

        $elementType = $this->typeLoader->load($elementTypeConfig);

        return new StaticListType($elementType);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return ['list'];
    }
}
