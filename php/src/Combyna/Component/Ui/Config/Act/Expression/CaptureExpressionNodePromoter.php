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
 * Class CaptureExpressionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureExpressionNodePromoter implements ExpressionNodeTypePromoterInterface
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
        return [CaptureExpressionNode::TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function promote(ExpressionNodeInterface $expressionNode)
    {
        if (!$expressionNode instanceof CaptureExpressionNode) {
            throw new InvalidArgumentException(sprintf(
                'Expected a "capture" expression node, got "%s"',
                $expressionNode->getType()
            ));
        }

        return $this->expressionFactory->createCaptureExpression($expressionNode->getCaptureName());
    }
}
