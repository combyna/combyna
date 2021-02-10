<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Plugin;

use Combyna\Component\Common\ComponentInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Interface PluginInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface PluginInterface extends ComponentInterface, CompilerPassInterface
{
    /**
     * Fetches any sub-plugins of this one. Sub-plugins will only be loaded
     * when using one of the originators that they specify
     *
     * @return SubPluginInterface[]
     */
    public function getSubPlugins();
}
