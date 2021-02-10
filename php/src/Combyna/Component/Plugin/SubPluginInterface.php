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

/**
 * Interface SubPluginInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SubPluginInterface extends PluginInterface
{
    /**
     * Fetches the list of possible originators that must have been used
     * in order for this sub-plugin to be loaded
     *
     * @return string[]
     */
    public function getSupportedOriginators();
}
