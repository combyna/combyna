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
use Combyna\Component\Validator\Type\MultipleTypeDeterminer;
use Combyna\Component\Validator\Type\UnresolvedTypeDeterminer;
use InvalidArgumentException;

/**
 * Class MultipleTypeLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MultipleTypeLoader implements TypeTypeLoaderInterface
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
        try {
            $subTypesConfig = $this->configParser->getElement(
                $config,
                'types',
                'sub-types',
                ['array']
            );
        } catch (InvalidArgumentException $exception) {
            return new UnresolvedTypeDeterminer($exception->getMessage());
        }

        $subTypeDeterminers = array_map(function ($subTypeConfig) {
            return $this->typeLoader->load($subTypeConfig);
        }, $subTypesConfig);

        return new MultipleTypeDeterminer($subTypeDeterminers);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return ['multiple'];
    }
}
