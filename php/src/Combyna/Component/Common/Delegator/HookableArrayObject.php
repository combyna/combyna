<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Common\Delegator;

use ArrayAccess;

/**
 * Class HookableArrayObject
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class HookableArrayObject implements ArrayAccess
{
    /**
     * @var callable|null
     */
    private $onIsSet = null;

    /**
     * @var callable|null
     */
    private $onUnset = null;

    /**
     * @var array
     */
    private $wrappedArray;

    /**
     * @param array $wrappedArray
     */
    public function __construct(array $wrappedArray)
    {
        $this->wrappedArray = $wrappedArray;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $isSet = array_key_exists($offset, $this->wrappedArray);

        if ($this->onIsSet) {
            $onIsSet = $this->onIsSet;

            $onIsSet($offset, $isSet);
        }

        return $isSet;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->wrappedArray[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->wrappedArray[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $value = $this->wrappedArray[$offset];

        unset($this->wrappedArray[$offset]);

        if ($this->onUnset) {
            $onUnset = $this->onUnset;

            $onUnset($offset, $value);
        }
    }

    /**
     * Sets the callback to be called when an element's existence is queried
     *
     * @param callable|null $onIsSet
     */
    public function onIsSet(callable $onIsSet = null)
    {
        $this->onIsSet = $onIsSet;
    }

    /**
     * Sets the callback to be called when an element is unset
     *
     * @param callable|null $onUnset
     */
    public function onUnset(callable $onUnset = null)
    {
        $this->onUnset = $onUnset;
    }

    /**
     * Fetches the wrapped array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->wrappedArray;
    }
}
