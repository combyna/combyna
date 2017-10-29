<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Config\Loader;

use Combyna\Component\App\Config\Act\HomeNode;

/**
 * Interface HomeLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface HomeLoaderInterface
{
    /**
     * Creates a home ACT node from the specified config array
     *
     * @param array $homeConfig
     * @return HomeNode
     */
    public function loadHome(array $homeConfig);
}
