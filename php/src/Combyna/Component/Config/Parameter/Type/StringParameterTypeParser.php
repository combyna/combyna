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
 * Class StringParameterTypeParser
 *
 * Parameter type for native string arguments. This differs from the Text parameter type
 * in that this type expects only a raw string, while the Text type expects a Text static.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StringParameterTypeParser implements ParameterTypeTypeParserInterface
{
    /**
     * Determines whether an argument is valid for the parameter
     *
     * @param StringParameterType $type
     * @param mixed $value
     * @return bool
     */
    public function argumentIsValid(
        StringParameterType $type,
        $value
    ) {
        return is_string($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToParserCallableMap()
    {
        return [
            StringParameterType::TYPE => [$this, 'parseArgument']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToArgumentValidityCallableMap()
    {
        return [
            StringParameterType::TYPE => [$this, 'argumentIsValid']
        ];
    }

    /**
     * Fetches the actual argument value for this type from its raw value
     *
     * @param StringParameterType $type
     * @param string $rawValue
     * @return mixed
     */
    public function parseArgument(
        StringParameterType $type,
        $rawValue
    ) {
        return $rawValue;
    }
}
