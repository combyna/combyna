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
 * Interface FixedMutableStaticBagInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FixedMutableStaticBagInterface extends MutableStaticBagInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws InvalidStaticException Throws when the provided value does not match the type
     *                                defined for the model of this bag
     */
    public function setStatic($name, StaticInterface $value);
}
