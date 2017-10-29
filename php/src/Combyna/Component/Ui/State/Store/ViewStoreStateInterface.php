<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\Store;

use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Ui\Store\Config\Act\ViewStoreNode;

/**
 * Interface ViewStoreStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewStoreStateInterface extends UiStoreStateInterface
{
    const TYPE = ViewStoreNode::TYPE;

    /**
     * Either creates a new view store state with the specified static slot value
     * or just returns the current one, if it already has the same value
     *
     * @param string $slotName
     * @param StaticInterface $newSlotStatic
     * @return ViewStoreStateInterface
     */
    public function withSlotStatic($slotName, StaticInterface $newSlotStatic);
}
