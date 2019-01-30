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
 * Interface ParameterTypeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ParameterTypeInterface
{
    /**
     * Fetches a string summary of the type
     *
     * @return string
     */
    public function getSummary();

    /**
     * Fetches the unique type for this parameter type
     *
     * @return string
     */
    public function getType();
}
