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
use Combyna\Component\Expression\AbstractExpressionFactory;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Ui\Store\Expression\SlotExpression;
use Combyna\Component\Ui\Store\Expression\ViewStoreQueryExpression;

/**
 * Class UiExpressionFactory
 *
 * Creates expression or static expression objects inside UIs
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UiExpressionFactory extends AbstractExpressionFactory implements UiExpressionFactoryInterface
{
    /**
     * @param ExpressionFactoryInterface $parentExpressionFactory
     */
    public function __construct(ExpressionFactoryInterface $parentExpressionFactory)
    {
        parent::__construct($parentExpressionFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function createStoreSlotExpression($slotName)
    {
        return new SlotExpression($slotName);
    }

    /**
     * {@inheritdoc}
     */
    public function createViewStoreQueryExpression($queryName, ExpressionBagInterface $argumentExpressionBag)
    {
        return new ViewStoreQueryExpression($this, $queryName, $argumentExpressionBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetAttributeExpression($attributeName)
    {
        return new WidgetAttributeExpression($this, $attributeName);
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetValueExpression($valueName)
    {
        return new WidgetValueExpression($this, $valueName);
    }
}
