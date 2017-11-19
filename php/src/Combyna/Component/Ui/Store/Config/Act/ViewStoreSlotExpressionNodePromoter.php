<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Act;

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeTypePromoterInterface;
use Combyna\Component\Ui\Expression\UiExpressionFactoryInterface;
use InvalidArgumentException;

/**
 * Class ViewStoreSlotExpressionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreSlotExpressionNodePromoter implements ExpressionNodeTypePromoterInterface
{
    /**
     * @var UiExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @param UiExpressionFactoryInterface $expressionFactory
     * @param BagNodePromoter $bagNodePromoter
     */
    public function __construct(UiExpressionFactoryInterface $expressionFactory)
    {
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return [ViewStoreSlotExpressionNode::TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function promote(ExpressionNodeInterface $expressionNode)
    {
        if (!$expressionNode instanceof ViewStoreSlotExpressionNode) {
            throw new InvalidArgumentException(
                'Expected a "view-store-slot" expression node, got "' . $expressionNode->getType() . '"'
            );
        }

        return $this->expressionFactory->createStoreSlotExpression($expressionNode->getSlotName());
    }
}
