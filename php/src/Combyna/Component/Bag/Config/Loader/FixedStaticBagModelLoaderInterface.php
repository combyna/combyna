<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Config\Loader;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;

/**
 * Interface FixedStaticBagModelLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FixedStaticBagModelLoaderInterface
{
    /**
     * Creates a FixedStaticBagModel ACT node from the provided array structure
     *
     * @param array $modelConfig
     * @return FixedStaticBagModelNode
     */
    public function load(array $modelConfig);
}
