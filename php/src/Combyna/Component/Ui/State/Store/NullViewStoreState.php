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

use Combyna\Component\Expression\StaticInterface;
use InvalidArgumentException;

/**
 * Class NullViewStoreState
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NullViewStoreState implements NullViewStoreStateInterface
{
    /**
     * @var string
     */
    private $storeViewName;

    /**
     * @param string $storeViewName
     */
    public function __construct($storeViewName)
    {
        $this->storeViewName = $storeViewName;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlotStatic($name)
    {
        throw new InvalidArgumentException(sprintf(
            'Null store state for view "%s" cannot provide slot static "%s", as they define none',
            $this->storeViewName,
            $name
        ));
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
        throw new InvalidArgumentException(sprintf(
            'Null store state for view "%s" cannot assign slot static "%s", as they define none',
            $this->storeViewName,
            $slotName
        ));
    }
}
