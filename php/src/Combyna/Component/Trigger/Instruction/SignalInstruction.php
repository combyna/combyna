<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Instruction;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Signal\DispatcherInterface;
use Combyna\Component\Signal\SignalDefinitionInterface;

/**
 * Class SignalInstruction
 *
 * An instruction for only triggers to use that dispatches a signal via the dispatcher
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalInstruction implements InstructionInterface
{
    /**
     * @var DispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ExpressionBagInterface
     */
    private $payloadExpressionBag;

    /**
     * @var SignalDefinitionInterface
     */
    private $signalDefinition;

    /**
     * @param DispatcherInterface $dispatcher
     * @param SignalDefinitionInterface $signalDefinition
     * @param ExpressionBagInterface $payloadExpressionBag
     */
    public function __construct(
        DispatcherInterface $dispatcher,
        SignalDefinitionInterface $signalDefinition,
        ExpressionBagInterface $payloadExpressionBag
    ) {
        $this->dispatcher = $dispatcher;
        $this->payloadExpressionBag = $payloadExpressionBag;
        $this->signalDefinition = $signalDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function perform(
        EvaluationContextInterface $evaluationContext,
        ProgramStateInterface $programState,
        ProgramInterface $program
    ) {
        $payloadStaticBag = $this->payloadExpressionBag->toStaticBag($evaluationContext);

        return $this->dispatcher->dispatchSignal(
            $program,
            $programState,
            $this->signalDefinition,
            $payloadStaticBag
        );
    }
}
