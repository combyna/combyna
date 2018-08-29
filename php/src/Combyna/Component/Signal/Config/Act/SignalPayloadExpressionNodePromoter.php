<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Act;

use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeTypePromoterInterface;
use Combyna\Component\Signal\Expression\SignalExpressionFactoryInterface;
use InvalidArgumentException;

/**
 * Class SignalPayloadExpressionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalPayloadExpressionNodePromoter implements ExpressionNodeTypePromoterInterface
{
    /**
     * @var SignalExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @param SignalExpressionFactoryInterface $expressionFactory
     */
    public function __construct(SignalExpressionFactoryInterface $expressionFactory)
    {
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return [SignalPayloadExpressionNode::TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function promote(ExpressionNodeInterface $expressionNode)
    {
        if (!$expressionNode instanceof SignalPayloadExpressionNode) {
            throw new InvalidArgumentException(
                'Expected a "' . SignalPayloadExpressionNode::TYPE . '" expression node, got "' .
                $expressionNode->getType() . '"'
            );
        }

        return $this->expressionFactory->createSignalPayloadExpression($expressionNode->getStaticName());
    }
}
