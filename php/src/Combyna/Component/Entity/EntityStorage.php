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
use Combyna\Component\Expression\StaticInterface;
use InvalidArgumentException;

/**
 * Class EntityStorage
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EntityStorage implements EntityStorageInterface
{
    /**
     * @var FixedMutableStaticBagInterface
     */
    private $attributeBag;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var EntityStorageModelInterface
     */
    private $storageModel;

    /**
     * @param EntityStorageModelInterface $storageModel
     * @param FixedMutableStaticBagInterface $attributeBag
     * @param string $slug
     */
    public function __construct(
        EntityStorageModelInterface $storageModel,
        FixedMutableStaticBagInterface $attributeBag,
        $slug
    ) {
        // The bag provided must be fixed, so it must conform to a model,
        // but we need to ensure that the model it conforms to is the same one
        // used to define the attributes that may be stored for this entity
        $this->storageModel->assertValidStaticBag($attributeBag);

        if (!is_string($slug) && !is_int($slug)) {
            throw new InvalidArgumentException('Slug must be a string or integer, "' . gettype($slug) . '" given');
        }

        $this->attributeBag = $attributeBag;
        $this->slug = $slug;
        $this->storageModel = $storageModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatic($name)
    {
        return $this->attributeBag->getStatic($name);
    }

    /**
     * {@inheritdoc}
     */
    public function hasStatic($name)
    {
        return $this->attributeBag->hasStatic($name);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatic($name, StaticInterface $value)
    {
        $this->attributeBag->setStatic($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function toNativeArray()
    {
        return $this->attributeBag->toNativeArray();
    }

    /**
     * {@inheritdoc}
     */
    public function withStatic($name, StaticInterface $newStatic)
    {
        throw new \Exception('Not implemented');
    }
}
