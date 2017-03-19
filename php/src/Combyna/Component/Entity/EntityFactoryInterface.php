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

/**
 * Interface EntityFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EntityFactoryInterface
{
    /**
     * Creates a new entity of the specified model
     *
     * @param EntityModelInterface $entityModel
     * @param FixedMutableStaticBagInterface $attributeBag
     * @param string $slug
     * @return EntityInterface
     */
    public function createEntity(EntityModelInterface $entityModel, FixedMutableStaticBagInterface $attributeBag, $slug);
}
