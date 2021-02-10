<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

/**
 * Interface StaticValueInterface
 *
 * Base interface for both static expressions and their corresponding ACT nodes
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StaticValueInterface
{
    /**
     * Determines whether this value is equivalent to the given other value
     *
     * @param StaticValueInterface $otherValue
     * @return bool
     */
    public function equals(StaticValueInterface $otherValue);

    /**
     * Fetches a short summary of the static value, suitable for displaying
     * inside validation violations for example
     *
     * @return string
     */
    public function getSummary();

    /**
     * Fetches the native value of this static
     *
     * @return mixed
     */
    public function toNative();
}
