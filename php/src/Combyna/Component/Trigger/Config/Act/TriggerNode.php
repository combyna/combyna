<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Config\Act;

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class TriggerNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TriggerNode extends AbstractActNode
{
    /**
     * @var EventDefinitionReferenceNode
     */
    private $eventDefinitionReferenceNode;

    /**
     * @var InstructionNodeInterface[]
     */
    private $instructionNodes;

    /**
     * @param EventDefinitionReferenceNode $eventDefinitionReferenceNode
     * @param InstructionNodeInterface[] $instructionNodes
     */
    public function __construct(EventDefinitionReferenceNode $eventDefinitionReferenceNode, array $instructionNodes)
    {
        $this->eventDefinitionReferenceNode = $eventDefinitionReferenceNode;
        $this->instructionNodes = $instructionNodes;
    }

    /**
     * Fetches the event definition this trigger fires on
     *
     * @return EventDefinitionReferenceNode
     */
    public function getEventDefinitionReference()
    {
        return $this->eventDefinitionReferenceNode;
    }

    /**
     * Fetches the instructions to be executed when this trigger fires
     *
     * @return InstructionNodeInterface[]
     */
    public function getInstructions()
    {
        return $this->instructionNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->eventDefinitionReferenceNode->validate($subValidationContext);

        foreach ($this->instructionNodes as $instructionNode) {
            $instructionNode->validate($subValidationContext);
        }
    }
}
