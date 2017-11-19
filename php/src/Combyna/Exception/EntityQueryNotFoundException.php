<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Exception;

use Combyna\Component\Entity\EntityInterface;
use Exception;

/**
 * Class EntityQueryNotFoundException
 *
 * Thrown when a query is made against an entity but is not defined for it
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EntityQueryNotFoundException extends Exception
{
    /**
     * @var EntityInterface
     */
    private $entity;

    /**
     * @var string
     */
    private $queryName;

    /**
     * @param EntityInterface $entity
     * @param string $queryName
     */
    public function __construct(EntityInterface $entity, $queryName)
    {
        parent::__construct('Entity of model "' . $entity->getModelName() . '" has no query "' . $queryName . '"');

        $this->entity = $entity;
        $this->queryName = $queryName;
    }

    /**
     * Returns the entity this exception refers to
     *
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Returns the query name this exception refers to
     *
     * @return string
     */
    public function getQueryName()
    {
        return $this->queryName;
    }
}
