<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Instruction\Config\Act\InstructionNodeInterface;
use Combyna\Component\Signal\Validation\Context\Specifier\SignalHandlerContextSpecifier;
use Combyna\Component\Validator\Type\StaticTypeDeterminer;

/**
 * Class SignalHandlerNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalHandlerNode extends AbstractActNode
{
    const TYPE = 'signal-handler';

    /**
     * @var ExpressionNodeInterface|null
     */
    private $guardExpressionNode;

    /**
     * @var InstructionNodeInterface[]
     */
    private $instructionNodes;

    /**
     * @var SignalDefinitionReferenceNode
     */
    private $signalDefinitionReferenceNode;

    /**
     * @param SignalDefinitionReferenceNode $signalDefinitionReferenceNode
     * @param InstructionNodeInterface[] $instructionNodes
     * @param ExpressionNodeInterface|null $guardExpressionNode
     */
    public function __construct(
        SignalDefinitionReferenceNode $signalDefinitionReferenceNode,
        array $instructionNodes,
        ExpressionNodeInterface $guardExpressionNode = null
    ) {
        $this->guardExpressionNode = $guardExpressionNode;
        $this->instructionNodes = $instructionNodes;
        $this->signalDefinitionReferenceNode = $signalDefinitionReferenceNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        // Signal handler sub-context defines the signal's payload for child nodes to reference
        $specBuilder->defineValidationContext(new SignalHandlerContextSpecifier());

        $specBuilder->addChildNode($this->signalDefinitionReferenceNode);

        if ($this->guardExpressionNode !== null) {
            $specBuilder->addChildNode($this->guardExpressionNode);

            // Guard expression, if specified, must evaluate to a boolean
            // to decide whether the signal handler should be run or not
            $specBuilder->addConstraint(
                new ResultTypeConstraint(
                    $this->guardExpressionNode,
                    new StaticTypeDeterminer(BooleanExpression::class),
                    'guard'
                )
            );
        }

        foreach ($this->instructionNodes as $instructionNode) {
            $specBuilder->addChildNode($instructionNode);
        }
    }

    /**
     * Fetches the guard expression node that must evaluate to true for this handler to run
     *
     * @return ExpressionNodeInterface|null
     */
    public function getGuardExpression()
    {
        return $this->guardExpressionNode;
    }

    /**
     * Fetches the instruction nodes to be executed when this handler is run
     *
     * @return InstructionNodeInterface[]
     */
    public function getInstructions()
    {
        return $this->instructionNodes;
    }

    /**
     * Fetches the reference node to the definition of the signal that this handler responds to
     *
     * @return SignalDefinitionReferenceNode
     */
    public function getSignalDefinitionReference()
    {
        return $this->signalDefinitionReferenceNode;
    }
}
