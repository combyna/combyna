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

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;

/**
 * Interface ExpressionBagLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionBagLoaderInterface
{
    /**
     * Creates an ExpressionBag from the provided array structure
     *
     * @param array $bagConfig
     * @return ExpressionBagNode
     */
    public function load(array $bagConfig);
}
