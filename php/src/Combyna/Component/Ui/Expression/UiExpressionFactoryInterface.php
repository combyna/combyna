<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Expression;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Ui\Store\Expression\SlotExpression;
use Combyna\Component\Ui\Store\Expression\ViewStoreQueryExpression;

/**
 * Interface UiExpressionFactoryInterface
 *
 * Creates expression or static expression objects inside views
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface UiExpressionFactoryInterface extends ExpressionFactoryInterface
{
    /**
     * Creates a store SlotExpression
     *
     * @param string $slotName
     * @return SlotExpression
     */
    public function createStoreSlotExpression($slotName);

    /**
     * Creates a ViewStoreQueryExpression
     *
     * @param string $queryName
     * @param ExpressionBagInterface $argumentExpressionBag
     * @return ViewStoreQueryExpression
     */
    public function createViewStoreQueryExpression($queryName, ExpressionBagInterface $argumentExpressionBag);
}
