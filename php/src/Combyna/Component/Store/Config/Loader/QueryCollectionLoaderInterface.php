<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Store\Config\Loader;

use Combyna\Component\Store\Config\Act\QueryNode;

/**
 * Interface QueryCollectionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface QueryCollectionLoaderInterface
{
    /**
     * Creates a set of query ACT nodes from the specified config array
     *
     * @param array $config
     * @return QueryNode[]
     */
    public function loadCollection(array $config);
}
