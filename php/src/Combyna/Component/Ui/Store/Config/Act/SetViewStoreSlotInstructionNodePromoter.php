<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Act;

use Combyna\Component\Expression\Config\Act\DelegatingExpressionNodePromoter;
use Combyna\Component\Ui\Store\Instruction\SetViewStoreSlotInstruction;

/**
 * Class SetViewStoreSlotInstructionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SetViewStoreSlotInstructionNodePromoter implements ViewStoreInstructionNodeTypePromoterInterface
{
    /**
     * @var DelegatingExpressionNodePromoter
     */
    private $expressionNodePromoter;

    /**
     * @param DelegatingExpressionNodePromoter $expressionNodePromoter
     */
    public function __construct(DelegatingExpressionNodePromoter $expressionNodePromoter)
    {
        $this->expressionNodePromoter = $expressionNodePromoter;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToPromoterMethodMap()
    {
        return [
            SetViewStoreSlotInstructionNode::TYPE => 'promote'
        ];
    }

    /**
     * Promotes a SignalInstructionNode to a SignalInstruction
     *
     * @param SetViewStoreSlotInstructionNode $instructionNode
     * @return SetViewStoreSlotInstruction
     */
    public function promote(SetViewStoreSlotInstructionNode $instructionNode)
    {
        $valueExpression = $this->expressionNodePromoter->promote($instructionNode->getValueExpression());

        return new SetViewStoreSlotInstruction(
            $instructionNode->getSlotName(),
            $valueExpression
        );
    }
}
