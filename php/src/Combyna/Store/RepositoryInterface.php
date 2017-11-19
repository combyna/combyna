<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Store;

use Combyna\Component\Bag\FixedMutableStaticBagInterface;
use Combyna\Component\Entity\EntityContainerInterface;

/**
 * Interface RepositoryInterface
 *
 * Holds a set of pages of entities of a given model
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RepositoryInterface extends EntityContainerInterface
{
    /**
     * Creates and adds an entity object to this repository
     *
     * @param FixedMutableStaticBagInterface $attributeBag
     */
    public function addLocalEntity(FixedMutableStaticBagInterface $attributeBag);

    /**
     * Fetches the name of the model of entities this repository holds
     *
     * @return string
     */
    public function getEntityModelName();

    /**
     * Fetches the unique name of this repository within its store
     *
     * @return string
     */
    public function getName();
}
