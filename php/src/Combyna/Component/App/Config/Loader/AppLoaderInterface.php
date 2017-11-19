<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Config\Loader;

use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;

/**
 * Interface AppLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AppLoaderInterface
{
    /**
     * Creates an app ACT node from the specified config array
     *
     * @param EnvironmentNode $environmentNode
     * @param array $appConfig
     * @return AppNode
     */
    public function loadApp(EnvironmentNode $environmentNode, array $appConfig);
}
