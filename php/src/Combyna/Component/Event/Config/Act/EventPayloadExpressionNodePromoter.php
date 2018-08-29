<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Config\Act;

use Combyna\Component\Event\Expression\EventExpressionFactoryInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeTypePromoterInterface;
use InvalidArgumentException;

/**
 * Class EventPayloadExpressionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventPayloadExpressionNodePromoter implements ExpressionNodeTypePromoterInterface
{
    /**
     * @var EventExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @param EventExpressionFactoryInterface $expressionFactory
     */
    public function __construct(EventExpressionFactoryInterface $expressionFactory)
    {
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return [EventPayloadExpressionNode::TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function promote(ExpressionNodeInterface $expressionNode)
    {
        if (!$expressionNode instanceof EventPayloadExpressionNode) {
            throw new InvalidArgumentException(
                'Expected a "' . EventPayloadExpressionNode::TYPE . '" expression node, got "' .
                $expressionNode->getType() . '"'
            );
        }

        return $this->expressionFactory->createEventPayloadExpression($expressionNode->getStaticName());
    }
}
