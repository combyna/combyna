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

use Combyna\Component\Bag\Exception\InvalidStaticException;
use Combyna\Component\Expression\StaticInterface;

/**
 * Interface FixedImmutableStaticBagInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FixedImmutableStaticBagInterface extends StaticBagInterface
{
    /**
     * Creates a new static bag, with the provided static set to the given new value
     * (vs. a MutableStaticBag, which will modify the called object instead)
     *
     * @param string $name
     * @param StaticInterface $value
     * @return FixedImmutableStaticBagInterface
     * @throws InvalidStaticException Throws when the provided value does not match the type
     *                                defined for the model of this bag
     */
    public function setStatic($name, StaticInterface $value);
}
