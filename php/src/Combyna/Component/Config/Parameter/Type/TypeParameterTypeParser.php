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

use Combyna\Component\Type\Config\Loader\TypeLoaderInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Class TypeParameterTypeParser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TypeParameterTypeParser implements ParameterTypeTypeParserInterface
{
    /**
     * @var TypeLoaderInterface
     */
    private $typeLoader;

    /**
     * @param TypeLoaderInterface $typeLoader
     */
    public function __construct(TypeLoaderInterface $typeLoader)
    {
        $this->typeLoader = $typeLoader;
    }

    /**
     * Determines whether an argument is valid for the parameter
     *
     * @param TypeParameterType $type
     * @param mixed $value
     * @return bool
     */
    public function argumentIsValid(
        TypeParameterType $type,
        $value
    ) {
        // Allow the two different formats for types: just the type name as a single string,
        // or a config array with the type name under the property "type"
        // along with any other config specific to that type
        return is_string($value) || is_array($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToParserCallableMap()
    {
        return [
            TypeParameterType::TYPE => [$this, 'parseArgument']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToArgumentValidityCallableMap()
    {
        return [
            TypeParameterType::TYPE => [$this, 'argumentIsValid']
        ];
    }

    /**
     * Fetches the actual argument value for this type from its raw value
     *
     * @param TypeParameterType $type
     * @param string|array $rawValue
     * @return TypeDeterminerInterface
     */
    public function parseArgument(
        TypeParameterType $type,
        $rawValue
    ) {
        return $this->typeLoader->load($rawValue);
    }
}
