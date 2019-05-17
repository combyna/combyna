<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\Store;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\StaticInterface;

/**
 * Class ViewStoreState
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreState implements ViewStoreStateInterface
{
    /**
     * @var StaticBagInterface
     */
    private $slotStaticBag;

    /**
     * @var string
     */
    private $storeViewName;

    /**
     * @param string $storeViewName
     * @param StaticBagInterface $slotStaticBag
     */
    public function __construct($storeViewName, StaticBagInterface $slotStaticBag)
    {
        $this->slotStaticBag = $slotStaticBag;
        $this->storeViewName = $storeViewName;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlotStatic($name)
    {
        return $this->slotStaticBag->getStatic($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getStateName()
    {
        return $this->storeViewName;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreViewName()
    {
        return $this->storeViewName;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function withSlotStatic($slotName, StaticInterface $newSlotStatic)
    {
        $newSlotStaticBag = $this->slotStaticBag->withStatic($slotName, $newSlotStatic);

        if ($this->slotStaticBag === $newSlotStaticBag) {
            // The slot already has the specified static value, no need to create a new store state
            return $this;
        }

        return new self($this->storeViewName, $newSlotStaticBag);
    }
}
