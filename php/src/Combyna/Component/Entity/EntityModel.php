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
use InvalidArgumentException;

/**
 * Class EntityModel
 *
 * Defines a blueprint for how an entity should work
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EntityModel implements EntityModelInterface
{
    /**
     * @var CommandMethod[]
     */
    private $commandMethods;

    /**
     * @var string
     */
    private $name;

    /**
     * @var QueryMethod[]
     */
    private $queryMethods;

    /**
     * @var EntityStorageModelInterface
     */
    private $storageModel;

    /**
     * @param EntityStorageModelInterface $storageModel
     * @param CommandMethod[] $commandMethods
     * @param QueryMethod[] $queryMethods
     * @param string $name
     */
    public function __construct(
        EntityStorageModelInterface $storageModel,
        array $commandMethods,
        array $queryMethods,
        $name
    ) {
        $this->commandMethods = $commandMethods;
        $this->name = $name;
        $this->queryMethods = $queryMethods;
        $this->storageModel = $storageModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlugAttribute(FixedMutableStaticBagInterface $attributeBag)
    {
        return $this->storageModel->getSlugAttribute($attributeBag);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageModel()
    {
        return $this->storageModel;
    }

    /**
     * {@inheritdoc}
     */
    public function performCommand(
        $commandMethodName,
        StaticBagInterface $argumentStaticBag,
        EntityStorageInterface $storage
    ) {
        if (!array_key_exists($commandMethodName, $this->commandMethods)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid command method name "%s" given',
                $commandMethodName
            ));
        }

        $this->commandMethods[$commandMethodName]->perform($argumentStaticBag, $storage);
    }

    /**
     * {@inheritdoc}
     */
    public function validateStorage(FixedMutableStaticBagInterface $storage)
    {
        // Check that the attribute data provided meets the model defined for these entities
        $this->storageModel->assertValidStaticBag($storage);
    }
}
