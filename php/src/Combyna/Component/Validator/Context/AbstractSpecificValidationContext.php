<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Validator\ViolationInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Class AbstractSpecificValidationContext
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractSpecificValidationContext implements ValidationContextInterface
{
    /**
     * @var GenericValidationContextInterface
     */
    protected $genericContext;

    /**
     * @param GenericValidationContextInterface $genericContext
     */
    public function __construct(GenericValidationContextInterface $genericContext)
    {
        $this->genericContext = $genericContext;
    }

    /**
     * {@inheritdoc}
     */
    public function addDivisionByZeroViolation()
    {
        $this->genericContext->addDivisionByZeroViolation($this);
    }

    /**
     * {@inheritdoc}
     */
    public function addGenericViolation($description)
    {
        $this->genericContext->addGenericViolation($description, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function addTypeMismatchViolation(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        $contextDescription
    ) {
        $this->genericContext->addTypeMismatchViolation(
            $expectedType,
            $actualType,
            $contextDescription,
            $this
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addViolation(ViolationInterface $violation)
    {
        $this->genericContext->addViolation($violation);
    }

    /**
     * {@inheritdoc}
     */
    public function assertAllRequiredAssuredStaticsWereUsed()
    {
        $this->genericContext->assertAllRequiredAssuredStaticsWereUsed($this);
    }

    /**
     * {@inheritdoc}
     */
    public function assertAssured(
        ExpressionNodeInterface $expressionNode,
        $constraint,
        $contextDescription
    ) {
        $this->genericContext->assertAssured($expressionNode, $constraint, $contextDescription, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function assertAssuredStaticExists($assuredStaticName)
    {
        $this->genericContext->assertAssuredStaticExists($assuredStaticName, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function assertListResultType(ExpressionNodeInterface $expressionNode, $contextDescription)
    {
        $this->genericContext->assertListResultType($expressionNode, $contextDescription, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function assertOperator($operator, array $allowedOperators)
    {
        $this->genericContext->assertOperator($operator, $allowedOperators, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function assertPossibleMatchingResultTypes(
        ExpressionNodeInterface $leftOperandExpressionNode,
        $leftOperandContextDescription,
        ExpressionNodeInterface $rightOperandExpressionNode,
        $rightOperandContextDescription,
        array $allowedMatchingResultTypes
    ) {
        $this->genericContext->assertPossibleMatchingResultTypes(
            $leftOperandExpressionNode,
            $leftOperandContextDescription,
            $rightOperandExpressionNode,
            $rightOperandContextDescription,
            $allowedMatchingResultTypes,
            $this
        );
    }

    /**
     * {@inheritdoc}
     */
    public function assertResultType(
        ExpressionNodeInterface $expressionNode,
        TypeInterface $allowedType,
        $contextDescription
    ) {
        $this->genericContext->assertResultType(
            $expressionNode,
            $allowedType,
            $contextDescription,
            $this
        );
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidFunctionCall(
        $libraryName,
        $functionName,
        ExpressionBagNode $argumentExpressionBagNode
    ) {
        $this->genericContext->assertValidFunctionCall(
            $libraryName,
            $functionName,
            $argumentExpressionBagNode,
            $this
        );
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidSignal($libraryName, $signalName)
    {
        $this->genericContext->assertValidSignal($libraryName, $signalName, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function assertVariableExists($variableName)
    {
        $this->genericContext->assertVariableExists($variableName, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubAssuredContext(array $assuranceNodes)
    {
        return $this->genericContext->createSubAssuredContext($assuranceNodes);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubActNodeContext(ActNodeInterface $actNode)
    {
        return $this->genericContext->createSubActNodeContext($actNode);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubScopeContext()
    {
        return $this->genericContext->createSubScopeContext();
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStaticAssurance($assuredStaticName)
    {
        return $this->genericContext->getAssuredStaticAssurance($assuredStaticName, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStaticType($assuredStaticName)
    {
        return $this->genericContext->getAssuredStaticType($assuredStaticName, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctionReturnType($libraryName, $functionName)
    {
        return $this->genericContext->getFunctionReturnType($libraryName, $functionName, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->genericContext->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getVariableType($variableName)
    {
        return $this->genericContext->getVariableType($variableName);
    }
}
