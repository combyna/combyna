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
 * Interface DefinedCompoundWidgetStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DefinedCompoundWidgetStateInterface extends DefinedWidgetStateInterface
{
    const TYPE = 'defined-compound-widget';

    /**
     * Fetches the state of the root widget of the compound widget
     *
     * @return WidgetStateInterface
     */
    public function getRootWidgetState();

    /**
     * Either creates a new widget state with the specified new sub-states
     * or just returns the current one, if it already has all of the same sub-states
     *
     * @param StaticBagInterface $attributeStaticBag
     * @param StaticBagInterface $valueStaticBag
     * @param WidgetStateInterface[] $childWidgetStates
     * @param WidgetStateInterface $rootWidgetState
     * @return DefinedCompoundWidgetStateInterface
     */
    public function with(
        StaticBagInterface $attributeStaticBag,
        StaticBagInterface $valueStaticBag,
        array $childWidgetStates,
        WidgetStateInterface $rootWidgetState
    );
}
