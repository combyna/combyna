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

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNode;
use Combyna\Component\Trigger\Validation\Context\Specifier\TriggerContextSpecifier;

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
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->eventDefinitionReferenceNode);

        $specBuilder->addSubSpec(function (BehaviourSpecBuilderInterface $subSpecBuilder) {
            // Trigger sub-context defines the event's payload for instruction nodes to reference
            $subSpecBuilder->defineValidationContext(new TriggerContextSpecifier());

            foreach ($this->instructionNodes as $instructionNode) {
                $subSpecBuilder->addChildNode($instructionNode);
            }
        });
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
}
