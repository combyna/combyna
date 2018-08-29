<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Signal;

use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Signal\SignalDefinitionReferenceInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\Store\Evaluation\ViewStoreEvaluationContextInterface;
use Combyna\Component\Ui\Store\Instruction\ViewStoreInstructionListInterface;

/**
 * Class ViewStoreSignalHandler
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreSignalHandler implements ViewStoreSignalHandlerInterface
{
    /**
     * @var ExpressionInterface|null
     */
    private $guardExpression;

    /**
     * @var ViewStoreInstructionListInterface
     */
    private $instructionList;

    /**
     * @var SignalDefinitionReferenceInterface
     */
    private $signalDefinitionReference;

    /**
     * @param SignalDefinitionReferenceInterface $signalDefinitionReference
     * @param ViewStoreInstructionListInterface $instructionList
     * @param ExpressionInterface|null $guardExpression
     */
    public function __construct(
        SignalDefinitionReferenceInterface $signalDefinitionReference,
        ViewStoreInstructionListInterface $instructionList,
        ExpressionInterface $guardExpression = null
    ) {
        $this->guardExpression = $guardExpression;
        $this->instructionList = $instructionList;
        $this->signalDefinitionReference = $signalDefinitionReference;
    }

    /**
     * {@inheritdoc}
     */
    public function handleSignal(
        ViewStoreStateInterface $viewStoreState,
        SignalInterface $signal,
        ViewStoreEvaluationContextInterface $storeEvaluationContext
    ) {
        if (
            $signal->getLibraryName() !== $this->signalDefinitionReference->getLibraryName() ||
            $signal->getName() !== $this->signalDefinitionReference->getSignalName()
        ) {
            // Signal is not for us: nothing to do
            return $viewStoreState;
        }

        // Create a context to allow access to the payload of the signal being handled
        $signalEvaluationContext = $storeEvaluationContext->createSubSignalEvaluationContext($signal);

        return $this->instructionList->performAll($signalEvaluationContext, $viewStoreState);
    }
}
