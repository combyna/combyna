<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Parameter\Type;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoaderInterface;

/**
 * Class FixedStaticBagModelParameterTypeParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedStaticBagModelParameterTypeParser implements ParameterTypeTypeParserInterface
{
    /**
     * @var FixedStaticBagModelLoaderInterface
     */
    private $fixedStaticBagModelLoader;

    /**
     * @param FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader
     */
    public function __construct(FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader)
    {
        $this->fixedStaticBagModelLoader = $fixedStaticBagModelLoader;
    }

    /**
     * Determines whether an argument is valid for the parameter
     *
     * @param FixedStaticBagModelParameterType $type
     * @param mixed $value
     * @return bool
     */
    public function argumentIsValid(
        FixedStaticBagModelParameterType $type,
        $value
    ) {
        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $typeConfig) {
            // Each element must be either the name of a type or a type descriptor array
            if (!is_string($value) && !is_array($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToParserCallableMap()
    {
        return [
            FixedStaticBagModelParameterType::TYPE => [$this, 'parseArgument']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToArgumentValidityCallableMap()
    {
        return [
            FixedStaticBagModelParameterType::TYPE => [$this, 'argumentIsValid']
        ];
    }

    /**
     * Fetches the actual argument value for this type from its raw value
     *
     * @param FixedStaticBagModelParameterType $type
     * @param array $rawValue
     * @return FixedStaticBagModelNode
     */
    public function parseArgument(
        FixedStaticBagModelParameterType $type,
        array $rawValue
    ) {
        return $this->fixedStaticBagModelLoader->load($rawValue);
    }
}
