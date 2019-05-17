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
 * Interface ParameterTypeParserInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ParameterTypeParserInterface
{
    /**
     * Determines whether the given argument is valid for a parameter of this type
     *
     * @param ParameterTypeInterface $parameterType
     * @param mixed $value
     * @return bool
     */
    public function argumentIsValid(ParameterTypeInterface $parameterType, $value);

    /**
     * Fetches the actual argument value for this type from its raw value
     *
     * @param ParameterTypeInterface $parameterType
     * @param mixed $rawValue
     * @return mixed
     */
    public function parseArgument(ParameterTypeInterface $parameterType, $rawValue);
}
