<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Store\Entity;

use Combyna\Component\Entity\EntityModelInterface;

/**
 * Class EntityRepository
 *
 * Contains an entity model and a set
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EntityRepository
{
    /**
     * A model that defines the data structure, queries and commands for a type of entity
     *
     * @var EntityModelInterface
     */
    private $entityModel;

    private $localEntityCollection;

    private $remoteEntityCollections = [];
}
