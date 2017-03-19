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
}
