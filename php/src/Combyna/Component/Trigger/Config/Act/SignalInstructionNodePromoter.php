<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Config\Act;

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Signal\DispatcherInterface;
use Combyna\Component\Trigger\Instruction\SignalInstruction;

/**
 * Class SignalInstructionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalInstructionNodePromoter implements InstructionNodeTypePromoterInterface
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var DispatcherInterface
     */
    private $dispatcher;

    /**
     * @param BagNodePromoter $bagNodePromoter
     * @param DispatcherInterface $dispatcher
     */
    public function __construct(
        BagNodePromoter $bagNodePromoter,
        DispatcherInterface $dispatcher
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToPromoterMethodMap()
    {
        return [
            SignalInstructionNode::TYPE => 'promote'
        ];
    }

    /**
     * Promotes a SignalInstructionNode to a SignalInstruction
     *
     * @param SignalInstructionNode $instructionNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @return SignalInstruction
     */
    public function promote(SignalInstructionNode $instructionNode, ResourceRepositoryInterface $resourceRepository)
    {
        $payloadStaticBag = $this->bagNodePromoter->promoteExpressionBag(
            $instructionNode->getPayloadExpressionBagNode()
        );
        $signalDefinition = $resourceRepository->getSignalDefinitionByName(
            $instructionNode->getSignalLibraryName(),
            $instructionNode->getSignalName()
        );

        return new SignalInstruction(
            $this->dispatcher,
            $signalDefinition,
            $payloadStaticBag
        );
    }
}
