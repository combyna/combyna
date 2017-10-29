<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\View;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Ui\State\View\EmbedViewStateInterface;

/**
 * Interface EmbedViewInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EmbedViewInterface extends ViewInterface
{
    /**
     * Checks that the provided static bag is a valid set of attributes for this view
     *
     * @param StaticBagInterface $attributeStaticBag
     */
    public function assertValidAttributeStaticBag(StaticBagInterface $attributeStaticBag);

    /**
     * Creates an initial state for the embed view
     *
     * @param StaticBagInterface $viewAttributeStaticBag
     * @return EmbedViewStateInterface
     */
    public function createInitialState(StaticBagInterface $viewAttributeStaticBag);
}
