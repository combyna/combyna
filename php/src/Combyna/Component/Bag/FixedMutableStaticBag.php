<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag;

use Combyna\Component\Expression\StaticInterface;

/**
 * Class FixedMutableStaticBag
 *
 * Represents a bag of assorted name/value pairs, where the values must be StaticInterface objects
 * that meet the specification defined by a model and can be modified
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedMutableStaticBag implements FixedMutableStaticBagInterface
{
    /**
     * @var FixedStaticBagModelInterface
     */
    private $staticBagModel;

    /**
     * @var MutableStaticBag
     */
    private $looseBag;

    /**
     * @param FixedStaticBagModelInterface $staticBagModel
     * @param MutableStaticBag $looseBag
     */
    public function __construct(
        FixedStaticBagModelInterface $staticBagModel,
        MutableStaticBag $looseBag
    ) {
        $staticBagModel->assertValidStaticBag($looseBag);

        $this->looseBag = $looseBag;
        $this->staticBagModel = $staticBagModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatic($name)
    {
        return $this->looseBag->hasStatic($name);
    }

    /**
     * {@inheritdoc}
     */
    public function hasStatic($name)
    {
        return $this->looseBag->hasStatic($name);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatic($name, StaticInterface $value)
    {
        // Check that this is a valid value for this static before assigning it
        $this->staticBagModel->assertValidStatic($name, $value);

        $this->looseBag->setStatic($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function toNativeArray()
    {
        return $this->looseBag->toNativeArray();
    }

    /**
     * {@inheritdoc}
     */
    public function withSlotStatic($slotName, StaticInterface $newSlotStatic)
    {
        throw new \Exception('Not implemented');
    }
}
