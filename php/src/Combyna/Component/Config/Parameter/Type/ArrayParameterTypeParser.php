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

/**
 * Class ArrayParameterTypeParser
 *
 * Parameter type for native array arguments
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ArrayParameterTypeParser implements ParameterTypeTypeParserInterface
{
    /**
     * Determines whether an argument is valid for the parameter
     *
     * @param ArrayParameterType $type
     * @param mixed $value
     * @return bool
     */
    public function argumentIsValid(
        ArrayParameterType $type,
        $value
    ) {
        return is_array($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToParserCallableMap()
    {
        return [
            ArrayParameterType::TYPE => [$this, 'parseArgument']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToArgumentValidityCallableMap()
    {
        return [
            ArrayParameterType::TYPE => [$this, 'argumentIsValid']
        ];
    }

    /**
     * Fetches the actual argument value for this type from its raw value
     *
     * @param ArrayParameterType $type
     * @param array $rawValue
     * @return mixed
     */
    public function parseArgument(
        ArrayParameterType $type,
        array $rawValue
    ) {
        return $rawValue;
    }
}
