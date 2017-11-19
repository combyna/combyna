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

use Combyna\Component\Bag\FixedMutableStaticBagInterface;

/**
 * Interface EntityStorageInterface
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EntityStorageInterface extends FixedMutableStaticBagInterface
{
    /**
     * Fetches the unique slug that references this entity
     *
     * @return string
     */
    public function getSlug();
}
