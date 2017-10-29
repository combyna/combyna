<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Store\Config\Loader;

use Combyna\Component\Store\Config\Act\QueryNode;

/**
 * Interface QueryLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface QueryLoaderInterface
{
    /**
     * Creates a query ACT node from the specified config array
     *
     * @param string $name
     * @param array $config
     * @return QueryNode
     */
    public function load($name, array $config);
}
