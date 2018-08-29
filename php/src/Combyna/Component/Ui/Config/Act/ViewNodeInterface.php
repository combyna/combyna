<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act;

use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Ui\Store\Config\Act\ViewStoreNode;

/**
 * Interface ViewNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewNodeInterface extends ActNodeInterface
{
    /**
     * Fetches the unique name of this view
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the store for this view, if set
     *
     * @return ViewStoreNode|null
     */
    public function getStore();
}
