<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Entity;

use Combyna\Component\Bag\FixedMutableStaticBagInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\StaticInterface;

/**
 * Interface EntityStorageModelInterface
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EntityStorageModelInterface
{
    /**
     * Checks that the static is defined and matches its type for this model
     *
     * @param string $name
     * @param StaticInterface $value
     */
    public function assertValidStatic($name, StaticInterface $value);

    /**
     * Checks that all statics in the provided bag are defined and match their types for this model
     *
     * @param StaticBagInterface $staticBag
     */
    public function assertValidStaticBag(StaticBagInterface $staticBag);

    /**
     * Fetches the native value of the attribute used for slugs of this entity
     *
     * @param FixedMutableStaticBagInterface $attributeBag
     * @return string
     */
    public function getSlugAttribute(FixedMutableStaticBagInterface $attributeBag);
}
