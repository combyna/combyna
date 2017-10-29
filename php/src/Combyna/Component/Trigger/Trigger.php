<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger;

use Combyna\Component\Event\EventDefinitionReferenceInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Trigger\Instruction\InstructionListInterface;
use Combyna\Component\Ui\Evaluation\WidgetEvaluationContextInterface;

/**
 * Class Trigger
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Trigger implements TriggerInterface
{
    /**
     * @var EventDefinitionReferenceInterface
     */
    private $eventDefinitionReference;

    /**
     * @var InstructionListInterface
     */
    private $instructionList;

    /**
     * @param EventDefinitionReferenceInterface $eventDefinitionReference
     * @param InstructionListInterface $instructionList
     */
    public function __construct(
        EventDefinitionReferenceInterface $eventDefinitionReference,
        InstructionListInterface $instructionList
    ) {
        $this->eventDefinitionReference = $eventDefinitionReference;
        $this->instructionList = $instructionList;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventLibraryName()
    {
        return $this->eventDefinitionReference->getLibraryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getEventName()
    {
        return $this->eventDefinitionReference->getEventName();
    }

    /**
     * {@inheritdoc}
     */
    public function invoke(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        EventInterface $event,
        WidgetEvaluationContextInterface $widgetEvaluationContext
    ) {
        $eventEvaluationContext = $widgetEvaluationContext->createSubEventEvaluationContext($event);

        return $this->instructionList->performAll($eventEvaluationContext, $programState, $program);
    }
}
