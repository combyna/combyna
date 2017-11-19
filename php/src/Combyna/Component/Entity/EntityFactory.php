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
 * Class EntityFactory
 *
 * Creates objects related to entities
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EntityFactory implements EntityFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createEntity(
        EntityModelInterface $entityModel,
        FixedMutableStaticBagInterface $attributeBag,
        $slug
    ) {
        $entityStorage = new EntityStorage($entityModel->getStorageModel(), $attributeBag, $slug);
        
        return new Entity($entityModel, $entityStorage);
    }
}
