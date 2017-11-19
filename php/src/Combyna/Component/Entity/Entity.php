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

use Combyna\Component\Bag\StaticBagInterface;

/**
 * Class Entity
 *
 * Represents an instance of an EntityModel
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Entity implements EntityInterface
{
    /**
     * @var EntityModelInterface
     */
    private $entityModel;

    /**
     * @var EntityStorageInterface
     */
    private $storage;

    /**
     * @param EntityModelInterface $entityModel
     * @param EntityStorageInterface $storage
     */
    public function __construct(EntityModelInterface $entityModel, EntityStorageInterface $storage)
    {
        $entityModel->validateStorage($storage);

        $this->entityModel = $entityModel;
        $this->storage = $storage;
    }

    /**
     * Determines whether this entity defines the specified command
     *
     * @param string $commandName
     * @return bool
     */
    public function definesCommand($commandName)
    {
        return $this->entityModel->definesCommand($commandName);
    }

    /**
     * Determines whether this entity defines the specified query
     *
     * @param string $queryName
     * @return bool
     */
    public function definesQuery($queryName)
    {
        return $this->entityModel->definesQuery($queryName);
    }

    /**
     * {@inheritdoc}
     */
    public function getModelName()
    {
        return $this->entityModel->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->storage->getSlug();
    }

    /**
     * {@inheritdoc}
     */
    public function makeQuery($queryName, StaticBagInterface $argumentStaticBag)
    {
        return $this->entityModel->makeQuery($queryName, $argumentStaticBag, $this->storage);
    }

    /**
     * {@inheritdoc}
     */
    public function performCommand($commandName, StaticBagInterface $argumentStaticBag)
    {
        $this->entityModel->performCommand($commandName, $argumentStaticBag, $this->storage);
    }
}
