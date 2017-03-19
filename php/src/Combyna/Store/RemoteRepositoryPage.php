<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Store;

use Combyna\Component\Entity\EntityInterface;

/**
 * Class RemoteRepositoryPage
 *
 * Holds a single page of entities of a given model for a repository
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RemoteRepositoryPage implements RemoteRepositoryPageInterface
{
    /**
     * @var EntityInterface[]
     */
    private $entities;

    /**
     * @var int
     */
    private $index;

    /**
     * @param EntityInterface[] $entities
     * @param int $index
     */
    public function __construct(array $entities, $index)
    {
        $this->entities = $entities;
        $this->index = $index;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function hasEntityBySlug($slug)
    {
        return array_key_exists($slug, $this->entities);
    }
}
