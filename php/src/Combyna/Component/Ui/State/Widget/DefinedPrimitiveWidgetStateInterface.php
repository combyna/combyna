<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\Widget;

use Combyna\Component\Bag\StaticBagInterface;

/**
 * Interface DefinedPrimitiveWidgetStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DefinedPrimitiveWidgetStateInterface extends DefinedWidgetStateInterface
{
    const TYPE = 'defined-primitive-widget';

    /**
     * Either creates a new widget state with the specified new sub-states
     * or just returns the current one, if it already has all of the same sub-states
     *
     * @param StaticBagInterface $attributeStaticBag
     * @param StaticBagInterface $valueStaticBag
     * @param WidgetStateInterface[] $childWidgetStates
     * @return DefinedPrimitiveWidgetStateInterface
     */
    public function with(
        StaticBagInterface $attributeStaticBag,
        StaticBagInterface $valueStaticBag,
        array $childWidgetStates
    );
}
