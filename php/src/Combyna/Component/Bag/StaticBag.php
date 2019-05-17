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
 * Class StaticBag
 *
 * Represents a bag of related name/value pairs, where the values must be StaticInterface objects
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticBag implements StaticBagInterface
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
        $this->assertValidStatics($statics);

        $this->statics = $statics;
    }

    /**
     * Validates that all statics in the provided list are actually StaticInterfaces
     *
     * @param StaticInterface[] $statics
     */
    private function assertValidStatics(array $statics)
    {
        foreach ($statics as $name => $static) {
            if (!$static instanceof StaticInterface) {
                throw new InvalidArgumentException(
                    'Bag static "' . $name . '" is actually a ' . get_class($static)
                );
            }
        }
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
        if ($this->getStatic($name)->toNative() === $newStatic->toNative()) {
            // Static already has the provided value, no need to create a new static bag
            return $this;
        }

        return new self(array_merge($this->statics, [
            $name => $newStatic
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function withStatics(array $newStatics)
    {
        $mergedStatics = array_merge($this->statics, $newStatics);

        if ($mergedStatics === $this->statics) {
            return $this; // Just return this original bag if it was complete
        }

        return new StaticBag($mergedStatics);
    }
}
