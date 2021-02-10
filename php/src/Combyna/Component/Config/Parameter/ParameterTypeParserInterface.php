<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Parameter;

/**
 * Interface ParameterTypeParserInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ParameterTypeParserInterface
{
    /**
     * Fetches a map from parameter type name to the parser callable on this service
     *
     * @return array
     */
    public function getTypeToParserCallableMap();

    /**
     * Fetches a map from parameter type name to the validator callable on this service
     *
     * @return array
     */
    public function getTypeToValidatorCallableMap();
}
