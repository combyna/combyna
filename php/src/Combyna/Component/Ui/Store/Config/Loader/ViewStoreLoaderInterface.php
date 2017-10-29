<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Loader;

use Combyna\Component\Ui\Store\Config\Act\ViewStoreNode;

/**
 * Interface ViewStoreLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewStoreLoaderInterface
{
    /**
     * Creates a view store ACT node from the specified config array
     *
     * @param array $config
     * @return ViewStoreNode
     */
    public function load(array $config);
}
