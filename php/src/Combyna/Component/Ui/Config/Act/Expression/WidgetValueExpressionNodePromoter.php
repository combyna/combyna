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
 * Class WidgetValueExpressionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetValueExpressionNodePromoter implements ExpressionNodeTypePromoterInterface
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
        return [WidgetValueExpressionNode::TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function promote(ExpressionNodeInterface $expressionNode)
    {
        if (!$expressionNode instanceof WidgetValueExpressionNode) {
            throw new InvalidArgumentException(sprintf(
                'Expected a "widget value" expression node, got "%s"',
                $expressionNode->getType()
            ));
        }

        return $this->expressionFactory->createWidgetValueExpression($expressionNode->getValueName());
    }
}
