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

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Validator\Type\StaticListTypeDeterminer;

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
        $elementTypeConfig = $this->configParser->getOptionalElement(
            $config,
            'element',
            'type for elements',
            ['type' => 'any'],
            ['array', 'string'] // Type could just be a string or be an array for the expanded variant
        );

        $elementTypeDeterminer = $this->typeLoader->load($elementTypeConfig);

        return new StaticListTypeDeterminer($elementTypeDeterminer);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return ['list'];
    }
}
