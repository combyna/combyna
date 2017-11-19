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
 * Class ViewStoreQueryExpressionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreQueryExpressionNodePromoter implements ExpressionNodeTypePromoterInterface
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var UiExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @param UiExpressionFactoryInterface $expressionFactory
     * @param BagNodePromoter $bagNodePromoter
     */
    public function __construct(
        UiExpressionFactoryInterface $expressionFactory,
        BagNodePromoter $bagNodePromoter
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return [ViewStoreQueryExpressionNode::TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function promote(ExpressionNodeInterface $expressionNode)
    {
        if (!$expressionNode instanceof ViewStoreQueryExpressionNode) {
            throw new InvalidArgumentException(
                'Expected a "view-store-query" expression node, got "' . $expressionNode->getType() . '"'
            );
        }

        return $this->expressionFactory->createViewStoreQueryExpression(
            $expressionNode->getQueryName(),
            $this->bagNodePromoter->promoteExpressionBag($expressionNode->getArgumentExpressionBag())
        );
    }
}
