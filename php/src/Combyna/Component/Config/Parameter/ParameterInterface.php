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
 * Interface ParameterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ParameterInterface
{
    /**
     * Fetches the name of this parameter, unique within its list
     *
     * @return string|int
     */
    public function getName();

    /**
     * Fetches the unique type for this parameter
     *
     * @return string
     */
    public function getType();
}
