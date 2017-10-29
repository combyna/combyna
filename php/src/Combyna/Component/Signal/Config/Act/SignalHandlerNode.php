<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Act;

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Instruction\Config\Act\InstructionNodeInterface;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class SignalHandlerNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalHandlerNode extends AbstractActNode
{
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

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        if ($this->guardExpressionNode !== null) {
            $this->guardExpressionNode->validate($subValidationContext);

            $subValidationContext->assertResultType(
                $this->guardExpressionNode,
                new StaticType(BooleanExpression::class),
                'guard'
            );
        }

        $this->signalDefinitionReferenceNode->validate($subValidationContext);

        foreach ($this->instructionNodes as $instructionNode) {
            $instructionNode->validate($subValidationContext);
        }
    }
}
