<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Bag;

use Combyna\Expression\StaticInterface;

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
     * Assigns a new value for a static in this bag
     *
     * @param string $name
     * @param StaticInterface $value
     */
    public function setStatic($name, StaticInterface $value);
}
