<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Config\Act\Instruction;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodePromoterInterface;
use Combyna\Component\Router\Expression\RouterExpressionFactoryInterface;
use Combyna\Component\Router\Instruction\NavigateInstruction;
use Combyna\Component\Trigger\Config\Act\InstructionNodeTypePromoterInterface;

/**
 * Class NavigateInstructionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NavigateInstructionNodePromoter implements InstructionNodeTypePromoterInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var RouterExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ExpressionNodePromoterInterface
     */
    private $expressionNodePromoter;

    /**
     * @param BagFactoryInterface $bagFactory
     * @param RouterExpressionFactoryInterface $expressionFactory
     * @param ExpressionNodePromoterInterface $expressionNodePromoter
     */
    public function __construct(
        BagFactoryInterface $bagFactory,
        RouterExpressionFactoryInterface $expressionFactory,
        ExpressionNodePromoterInterface $expressionNodePromoter
    ) {
        $this->bagFactory = $bagFactory;
        $this->expressionFactory = $expressionFactory;
        $this->expressionNodePromoter = $expressionNodePromoter;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToPromoterMethodMap()
    {
        return [
            NavigateInstructionNode::TYPE => 'promote'
        ];
    }

    /**
     * Promotes a NavigateInstructionNode to a NavigateInstruction
     *
     * @param NavigateInstructionNode $instructionNode
     * @return NavigateInstruction
     */
    public function promote(NavigateInstructionNode $instructionNode)
    {
        return new NavigateInstruction(
            $this->expressionNodePromoter->promote($instructionNode->getRouteNameExpression()),
            $instructionNode->getRouteArgumentStructureExpression() !== null ?
                $this->expressionNodePromoter->promote($instructionNode->getRouteArgumentStructureExpression()) :
                $this->expressionFactory->createStructureExpression(
                    $this->bagFactory->createExpressionBag([])
                )
        );
    }
}
