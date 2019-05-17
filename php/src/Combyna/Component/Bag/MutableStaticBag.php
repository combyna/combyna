<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag;

use Combyna\Component\Expression\StaticInterface;
use InvalidArgumentException;

/**
 * Class MutableStaticBag
 *
 * Represents a bag of assorted name/value pairs, where the values must be StaticInterface objects
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MutableStaticBag implements MutableStaticBagInterface
{
    /**
     * @var StaticInterface[]
     */
    private $statics;

    /**
     * @param StaticInterface[] $statics
     */
    public function __construct(array $statics)
    {
        $this->statics = $statics;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatic($name)
    {
        if (!$this->hasStatic($name)) {
            throw new InvalidArgumentException(sprintf(
                'Static bag contains no "%s" static',
                $name
            ));
        }

        return $this->statics[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasStatic($name)
    {
        return array_key_exists($name, $this->statics);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatic($name, StaticInterface $value)
    {
        $this->statics[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function toNativeArray()
    {
        $nativeArray = [];

        foreach ($this->statics as $name => $static) {
            $nativeArray[$name] = $static->toNative();
        }

        return $nativeArray;
    }

    /**
     * {@inheritdoc}
     */
    public function withStatic($name, StaticInterface $newStatic)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function withStatics(array $newStatics)
    {
        throw new \Exception('Not implemented');
    }
}
