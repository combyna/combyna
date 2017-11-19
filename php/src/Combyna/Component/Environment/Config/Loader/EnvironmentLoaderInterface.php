<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Loader;

use Combyna\Component\Environment\Config\Act\EnvironmentNode;

/**
 * Interface EnvironmentLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EnvironmentLoaderInterface
{
    /**
     * Creates an EnvironmentNode from the given environment config
     *
     * @param array $environmentConfig
     * @return EnvironmentNode
     */
    public function loadEnvironment(array $environmentConfig);
}
