<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Store\View;

use Combyna\Component\Bag\FixedMutableStaticBagInterface;

/**
 * Class ViewStore
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStore implements ViewStoreInterface
{
    /**
     * @var StoreCommandInterface[]
     */
    private $commands;

    /**
     * @var StoreQueryInterface[]
     */
    private $queries;

    /**
     * @var FixedMutableStaticBagInterface
     */
    private $slotBag;
}
