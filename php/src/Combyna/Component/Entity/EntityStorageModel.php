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
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\StaticInterface;

/**
 * Class EntityStorageModel
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EntityStorageModel implements EntityStorageModelInterface
{
    /**
     * @var FixedStaticBagModelInterface
     */
    private $attributeBagModel;

    /**
     * @var string
     */
    private $slugAttributeName;

    /**
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @param string $slugAttributeName
     */
    public function __construct(FixedStaticBagModelInterface $attributeBagModel, $slugAttributeName)
    {
        $this->attributeBagModel = $attributeBagModel;
        $this->slugAttributeName = $slugAttributeName;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidStatic($name, StaticInterface $value)
    {
        $this->attributeBagModel->assertValidStatic($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidStaticBag(StaticBagInterface $attributeBag)
    {
        $this->attributeBagModel->assertValidStaticBag($attributeBag);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlugAttribute(FixedMutableStaticBagInterface $attributeBag)
    {
        return $attributeBag->getStatic($this->slugAttributeName)->toNative();
    }
}
