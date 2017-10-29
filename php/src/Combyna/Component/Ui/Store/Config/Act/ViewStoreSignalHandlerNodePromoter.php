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
use Combyna\Component\Signal\Config\Act\SignalDefinitionReferenceNodePromoter;
use Combyna\Component\Signal\Config\Act\SignalHandlerNode;
use Combyna\Component\Ui\Store\Signal\ViewStoreSignalHandlerInterface;
use Combyna\Component\Ui\Store\UiStoreFactoryInterface;

/**
 * Class ViewStoreSignalHandlerNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreSignalHandlerNodePromoter
{
    /**
     * @var DelegatingExpressionNodePromoter
     */
    private $expressionNodePromoter;

    /**
     * @var ViewStoreInstructionNodePromoterInterface
     */
    private $instructionNodePromoter;

    /**
     * @var SignalDefinitionReferenceNodePromoter
     */
    private $signalDefinitionReferenceNodePromoter;

    /**
     * @var UiStoreFactoryInterface
     */
    private $storeFactory;

    /**
     * @param UiStoreFactoryInterface $storeFactory
     * @param SignalDefinitionReferenceNodePromoter $signalDefinitionReferenceNodePromoter
     * @param ViewStoreInstructionNodePromoterInterface $instructionNodePromoter
     * @param DelegatingExpressionNodePromoter $expressionNodePromoter
     */
    public function __construct(
        UiStoreFactoryInterface $storeFactory,
        SignalDefinitionReferenceNodePromoter $signalDefinitionReferenceNodePromoter,
        ViewStoreInstructionNodePromoterInterface $instructionNodePromoter,
        DelegatingExpressionNodePromoter $expressionNodePromoter
    ) {
        $this->expressionNodePromoter = $expressionNodePromoter;
        $this->instructionNodePromoter = $instructionNodePromoter;
        $this->signalDefinitionReferenceNodePromoter = $signalDefinitionReferenceNodePromoter;
        $this->storeFactory = $storeFactory;
    }

    /**
     * Creates a ViewStoreSignalHandler from a SignalHandlerNode
     *
     * @param SignalHandlerNode $signalHandlerNode
     * @return ViewStoreSignalHandlerInterface
     */
    public function promote(SignalHandlerNode $signalHandlerNode)
    {
        $signalDefinitionReference = $this->signalDefinitionReferenceNodePromoter->promote(
            $signalHandlerNode->getSignalDefinitionReference()
        );
        $instructionList = $this->instructionNodePromoter->promoteList($signalHandlerNode->getInstructions());
        $guardExpression = $signalHandlerNode->getGuardExpression() !== null ?
            $this->expressionNodePromoter->promote($signalHandlerNode->getGuardExpression()) :
            null;

        return $this->storeFactory->createViewStoreSignalHandler(
            $signalDefinitionReference,
            $instructionList,
            $guardExpression
        );
    }

    /**
     * Creates an array of ViewStoreSignalHandlers
     *
     * @param SignalHandlerNode[] $signalHandlerNodes
     * @return ViewStoreSignalHandlerInterface[]
     */
    public function promoteCollection(array $signalHandlerNodes)
    {
        $storeSignalHandlers = array_map(function (SignalHandlerNode $signalHandlerNode) {
            return $this->promote($signalHandlerNode);
        }, $signalHandlerNodes);

        return $storeSignalHandlers;
    }
}
