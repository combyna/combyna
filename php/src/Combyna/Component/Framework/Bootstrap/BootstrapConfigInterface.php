<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Framework\Bootstrap;

use Combyna\Component\Plugin\PluginInterface;

/**
 * Interface BootstrapConfigInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BootstrapConfigInterface
{
    /**
     * Fetches the set of custom plugins to load (this is in addition to the required built-in set
     * that are specified in CombynaBootstrap)
     *
     * @return PluginInterface[]
     */
    public function getPlugins();
}
