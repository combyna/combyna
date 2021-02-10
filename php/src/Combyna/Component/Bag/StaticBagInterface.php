<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
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
     * Fetches the names of all statics in this bag
     *
     * @return string[]
     */
    public function getStaticNames();

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
     * Either creates a new static bag with the specified static value
     * or just returns the current one, if it already has the same static value
     *
     * @param string $name
     * @param StaticInterface $newStatic
     * @return StaticBagInterface
     */
    public function withStatic($name, StaticInterface $newStatic);

    /**
     * Either creates a new static bag with the specified static values
     * or just returns the current one, if it already has the same static values
     *
     * @param StaticInterface[] $newStatics
     * @return StaticBagInterface
     */
    public function withStatics(array $newStatics);
}
