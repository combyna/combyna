<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act\Expression;

use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeTypePromoterInterface;
use Combyna\Component\Ui\Expression\UiExpressionFactoryInterface;
use InvalidArgumentException;

/**
 * Class WidgetAttributeExpressionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetAttributeExpressionNodePromoter implements ExpressionNodeTypePromoterInterface
{
    /**
     * @var UiExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @param UiExpressionFactoryInterface $expressionFactory
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
        return [WidgetAttributeExpressionNode::TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function promote(ExpressionNodeInterface $expressionNode)
    {
        if (!$expressionNode instanceof WidgetAttributeExpressionNode) {
            throw new InvalidArgumentException(
                'Expected a "widget attribute" expression node, got "' . $expressionNode->getType() . '"'
            );
        }

        return $this->expressionFactory->createWidgetAttributeExpression($expressionNode->getAttributeName());
    }
}
