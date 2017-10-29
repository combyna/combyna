<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag;

use Combyna\Component\Expression\StaticInterface;
use InvalidArgumentException;

/**
 * Interface StaticBagInterface
 *
 * Contains a collection of related name->value pairs
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StaticBagInterface
{
    /**
     * Fetches the specified static from this bag
     *
     * @param string $name
     * @return StaticInterface
     * @throws InvalidArgumentException Throws when the bag does not contain the specified static
     */
    public function getStatic($name);

    /**
     * Determines whether this bag contains a static with the specified name
     *
     * @param string $name
     * @return bool
     */
    public function hasStatic($name);

    /**
     * Builds a native associative array of native values with the static names as the keys
     *
     * @return array
     */
    public function toNativeArray();

    /**
     * Either creates a new static bag with the specified slot static value
     * or just returns the current one, if it already has the same static value
     *
     * @param string $slotName
     * @param StaticInterface $newSlotStatic
     * @return StaticBagInterface
     */
    public function withSlotStatic($slotName, StaticInterface $newSlotStatic);
}
