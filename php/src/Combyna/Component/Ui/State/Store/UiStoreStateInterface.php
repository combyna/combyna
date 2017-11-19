<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\Store;

use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Ui\State\UiStateInterface;

/**
 * Interface UiStoreStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface UiStoreStateInterface extends UiStateInterface
{
    /**
     * Fetches the value of a slot in this UI store
     *
     * @param string $name
     * @return StaticInterface
     */
    public function getSlotStatic($name);

    /**
     * Fetches the unique name of this store within its parent view
     *
     * @return string
     */
    public function getStoreViewName();
}
