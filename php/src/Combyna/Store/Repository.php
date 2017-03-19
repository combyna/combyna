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

use Combyna\Component\Bag\FixedMutableStaticBagInterface;
use Combyna\Component\Entity\EntityFactoryInterface;
use Combyna\Component\Entity\EntityInterface;
use Combyna\Component\Entity\EntityModelInterface;
use InvalidArgumentException;

/**
 * Class Repository
 *
 * Holds a set of pages of entities of a given model
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Repository implements RepositoryInterface
{
    /**
     * @var EntityFactoryInterface
     */
    private $entityFactory;

    /**
     * @var EntityModelInterface
     */
    private $entityModel;

    /**
     * In theory we will only ever have one page of an entity loaded,
     * but when we need to display data that spans across multiple pages
     * it'll be handy to hold more than one in memory at once
     *
     * @var RemoteRepositoryPageInterface[]
     */
    private $loadedRemotePages = [];

    /**
     * @var EntityInterface[]
     */
    private $localEntities = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @var SlugGenerator
     */
    private $slugGenerator;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param EntityModelInterface $entityModel
     * @param SlugGenerator $slugGenerator
     * @param string $name
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        EntityModelInterface $entityModel,
        SlugGenerator $slugGenerator,
        $name
    ) {
        $this->entityFactory = $entityFactory;
        $this->entityModel = $entityModel;
        $this->name = $name;
        $this->slugGenerator = $slugGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function addLocalEntity(FixedMutableStaticBagInterface $attributeBag)
    {
        $slug = $this->slugGenerator->generateSlug(
            $this,
            EntityInterface::LOCAL_ENTITY_SLUG_PREFIX . $this->entityModel->getSlugAttribute($attributeBag)
        );
        $entity = $this->entityFactory->createEntity(
            $this->entityModel,
            $attributeBag,
            $slug
        );

        // Only need to check local entities - local & remote slugs cannot clash
        if (array_key_exists($slug, $this->localEntities)) {
            throw new InvalidArgumentException(
                'Repository already contains an entity with slug "' . $slug . '"'
            );
        }

        $this->localEntities[$slug] = $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityModelName()
    {
        return $this->entityModel->getName();
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
    public function hasEntityBySlug($slug)
    {
        if (strpos($slug, EntityInterface::LOCAL_ENTITY_SLUG_PREFIX) === 0) {
            return array_key_exists($slug, $this->localEntities);
        }

        // Check whether we have an entity with the slug in any of the loaded remote pages
        // (this may not include all possible entities, so we could return false for a slug
        // when there is in fact an entity with it, but we don't need to worry about that)
        foreach ($this->loadedRemotePages as $remotePage) {
            if ($remotePage->hasEntityBySlug($slug)) {
                return true;
            }
        }

        return false;
    }
}
