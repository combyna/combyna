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

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class SignalInstructionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalInstructionNode extends AbstractActNode implements InstructionNodeInterface
{
    const TYPE = 'signal';

    /**
     * @var ExpressionBagNode
     */
    private $payloadExpressionBagNode;

    /**
     * @var string
     */
    private $signalLibraryName;

    /**
     * @var string
     */
    private $signalName;

    /**
     * @param string $signalLibraryName
     * @param string $signalName
     * @param ExpressionBagNode $payloadExpressionBagNode
     */
    public function __construct($signalLibraryName, $signalName, ExpressionBagNode $payloadExpressionBagNode)
    {
        $this->payloadExpressionBagNode = $payloadExpressionBagNode;
        $this->signalLibraryName = $signalLibraryName;
        $this->signalName = $signalName;
    }

    /**
     * Fetches the bag of expressions to evaluate for the payload to dispatch with the signal
     *
     * @return ExpressionBagNode
     */
    public function getPayloadExpressionBagNode()
    {
        return $this->payloadExpressionBagNode;
    }

    /**
     * Fetches the unique name of the library that defines the signal to be dispatched by this instruction
     *
     * @return string
     */
    public function getSignalLibraryName()
    {
        return $this->signalLibraryName;
    }

    /**
     * Fetches the unique name of the signal within its library
     *
     * @return string
     */
    public function getSignalName()
    {
        return $this->signalName;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $subValidationContext->assertValidSignal($this->signalLibraryName, $this->signalName);
        $this->payloadExpressionBagNode->validate($subValidationContext);
    }
}
