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
use Combyna\Component\Entity\Exception\InvalidAttributeException;

/**
 * Interface EntityModelInterface
 *
 * Defines a blueprint for how an entity should work
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EntityModelInterface
{
    /**
     * Fetches the unique name for this model of entity, eg. `Product` or `User`
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the native value of the attribute used for slugs of this entity
     *
     * @param FixedMutableStaticBagInterface $attributeBag
     * @return string
     */
    public function getSlugAttribute(FixedMutableStaticBagInterface $attributeBag);

    /**
     * Fetches the storage model for entities of this model
     *
     * @return EntityStorageModelInterface
     */
    public function getStorageModel();

    /**
     * Checks that the provided initial storage data for an entity of this model meets
     * the specification defined by this model. Throws an exception otherwise
     *
     * @param FixedMutableStaticBagInterface $storage
     * @throws InvalidAttributeException
     */
    public function validateStorage(FixedMutableStaticBagInterface $storage);
}
