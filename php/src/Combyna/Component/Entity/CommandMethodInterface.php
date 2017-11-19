<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Entity;

use Combyna\Component\Bag\StaticBagInterface;

/**
 * Interface CommandMethodInterface
 *
 * Performs an operation on an entity
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface CommandMethodInterface
{
    /**
     * Performs the instructions of this command method in sequence
     *
     * @param StaticBagInterface $argumentStaticBag
     * @param EntityStorageInterface $storage
     */
    public function perform(StaticBagInterface $argumentStaticBag, EntityStorageInterface $storage);
}
