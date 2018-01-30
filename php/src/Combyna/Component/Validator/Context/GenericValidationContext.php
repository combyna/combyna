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
use Combyna\Component\Expression\Config\Act\Assurance\AssuranceNodeInterface;
use Combyna\Component\Expression\Config\Act\AssuredExpressionNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\ValidationFactoryInterface;
use Combyna\Component\Validator\ViolationInterface;

/**
 * Class GenericValidationContext
 *
 * Handles the work for all types of specific validation context
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class GenericValidationContext implements GenericValidationContextInterface
{
    /**
     * @var ActNodeInterface|null
     */
    private $actNode;

    /**
     * @var AssuranceNodeInterface[]
     */
    private $assuranceNodes;

    /**
     * @var TypeInterface[]
     */
    private $definedVariableTypes = [];

    /**
     * The parent generic context for this one, which could be a root one
     *
     * @var GenericValidationContextInterface
     */
    private $parentContext;

    /**
     * @var bool[]
     */
    private $usedAssuredStatics = [];

    /**
     * @var ValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param ValidationFactoryInterface $validationFactory
     * @param GenericValidationContextInterface $parentContext
     * @param AssuranceNodeInterface[] $assuranceNodes
     * @param ActNodeInterface|null $actNode
     */
    public function __construct(
        ValidationFactoryInterface $validationFactory,
        GenericValidationContextInterface $parentContext,
        array $assuranceNodes = [],
        ActNodeInterface $actNode = null
    ) {
        $this->actNode = $actNode;
        $this->assuranceNodes = $assuranceNodes;
        $this->parentContext = $parentContext;
        $this->validationFactory = $validationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function addDivisionByZeroViolation(ValidationContextInterface $currentContext)
    {
        $this->parentContext->addDivisionByZeroViolation($currentContext);
    }

    /**
     * {@inheritdoc}
     */
    public function addGenericViolation($description, ValidationContextInterface $currentContext)
    {
        $this->parentContext->addGenericViolation($description, $currentContext);
    }

    /**
     * {@inheritdoc}
     */
    public function addTypeMismatchViolation(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        $contextDescription,
        ValidationContextInterface $currentContext
    ) {
        $this->parentContext->addTypeMismatchViolation(
            $expectedType,
            $actualType,
            $contextDescription,
            $currentContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addViolation(ViolationInterface $violation)
    {
        $this->parentContext->addViolation($violation);
    }

    /**
     * {@inheritdoc}
     */
    public function assertAllRequiredAssuredStaticsWereUsed(
        ValidationContextInterface $currentContext
    ) {
        foreach ($this->assuranceNodes as $assuranceNode) {
            foreach ($assuranceNode->getRequiredAssuredStaticNames() as $requiredAssuredStaticName) {
                if (!array_key_exists($requiredAssuredStaticName, $this->usedAssuredStatics)) {
                    // This assurance defines this assured static, but it was not referenced

                    $this->addViolation($this->validationFactory->createGenericViolation(
                        'Assured static with name "' . $requiredAssuredStaticName . '" has not been used',
                        $currentContext
                    ));
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assertAssured(
        ExpressionNodeInterface $expressionNode,
        $constraint,
        $contextDescription,
        ValidationContextInterface $currentContext
    ) {
        if (!$expressionNode instanceof AssuredExpressionNode) {
            $this->addViolation($this->validationFactory->createGenericViolation(
                $contextDescription . ' expects "assured", got "' . $expressionNode->getType() . '"',
                $currentContext
            ));

            return;
        }

        $assuranceConstraint = $expressionNode->getAssurance($currentContext)->getConstraint();

        if ($assuranceConstraint !== $constraint) {
            $this->addViolation($this->validationFactory->createGenericViolation(
                $contextDescription . ' expects "' . $constraint . '" constraint, got "' . $assuranceConstraint . '"',
                $currentContext
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assertAssuredStaticExists(
        $assuredStaticName,
        ValidationContextInterface $currentContext
    ) {
        // Log that the assured static was checked for (will have been by an AssuredExpression)
        // to ensure that all required assured statics have been referenced by an AssuredExpression
        $this->usedAssuredStatics[$assuredStaticName] = true;

        if ($this->getOwnAssurance($assuredStaticName) !== null) {
            // This context defines the specified assured static
            return;
        }

        $this->parentContext->assertAssuredStaticExists($assuredStaticName, $currentContext);
    }

    /**
     * {@inheritdoc}
     */
    public function assertListResultType(
        ExpressionNodeInterface $expressionNode,
        $contextDescription,
        ValidationContextInterface $currentContext
    ) {
        $this->parentContext->assertListResultType($expressionNode, $contextDescription, $currentContext);
    }

    /**
     * {@inheritdoc}
     */
    public function assertOperator(
        $operator,
        array $allowedOperators,
        ValidationContextInterface $currentContext
    ) {
        $this->parentContext->assertOperator($operator, $allowedOperators, $currentContext);
    }

    /**
     * {@inheritdoc}
     */
    public function assertPossibleMatchingResultTypes(
        ExpressionNodeInterface $leftOperandExpressionNode,
        $leftOperandContextDescription,
        ExpressionNodeInterface $rightOperandExpressionNode,
        $rightOperandContextDescription,
        array $allowedMatchingResultTypes,
        ValidationContextInterface $currentContext
    ) {
        $this->parentContext->assertPossibleMatchingResultTypes(
            $leftOperandExpressionNode,
            $leftOperandContextDescription,
            $rightOperandExpressionNode,
            $rightOperandContextDescription,
            $allowedMatchingResultTypes,
            $currentContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function assertResultType(
        ExpressionNodeInterface $expressionNode,
        TypeInterface $allowedType,
        $contextDescription,
        ValidationContextInterface $currentContext
    ) {
        $this->parentContext->assertResultType($expressionNode, $allowedType, $contextDescription, $currentContext);
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidFunctionCall(
        $libraryName,
        $functionName,
        ExpressionBagNode $argumentExpressionBagNode,
        ValidationContextInterface $currentContext
    ) {
        $this->parentContext->assertValidFunctionCall(
            $libraryName,
            $functionName,
            $argumentExpressionBagNode,
            $currentContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidSignal(
        $libraryName,
        $signalName,
        ValidationContextInterface $currentContext
    ) {
        $this->parentContext->assertValidSignal($libraryName, $signalName, $currentContext);
    }

    /**
     * {@inheritdoc}
     */
    public function assertVariableExists(
        $variableName,
        ValidationContextInterface $currentContext
    ) {
        if (array_key_exists($variableName, $this->definedVariableTypes)) {
            // This context defines the specified variable
            return;
        }

        // See whether an ancestor context defines the specified variable
        $this->parentContext->assertVariableExists($variableName, $currentContext);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubAssuredContext(array $assuranceNodes)
    {
        return $this->validationFactory->createAssuredContext($this, $assuranceNodes);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubActNodeContext(ActNodeInterface $actNode)
    {
        return $this->validationFactory->createActNodeContext($this, $actNode);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubScopeContext()
    {
        return $this->validationFactory->createScopeContext($this);
    }

    /**
     * {@inheritdoc}
     */
    public function defineVariable($variableName, TypeInterface $type)
    {
        // TODO: Check ancestors for whether this variable has already been defined

        $this->definedVariableTypes[$variableName] = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStaticAssurance(
        $assuredStaticName,
        ValidationContextInterface $currentContext
    ) {
        $assurance = $this->getOwnAssurance($assuredStaticName);

        if ($assurance) {
            return $assurance;
        }

        return $this->parentContext->getAssuredStaticAssurance($assuredStaticName, $currentContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStaticType(
        $assuredStaticName,
        ValidationContextInterface $currentContext
    ) {
        $assurance = $this->getOwnAssurance($assuredStaticName);

        if ($assurance) {
            return $assurance->getStaticType($currentContext, $assuredStaticName);
        }

        return $this->parentContext->getAssuredStaticType($assuredStaticName, $currentContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctionReturnType(
        $libraryName,
        $functionName,
        ValidationContextInterface $currentContext
    ) {
        return $this->parentContext->getFunctionReturnType($libraryName, $functionName, $currentContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnAssurance($assuredStaticName)
    {
        foreach ($this->assuranceNodes as $assuranceNode) {
            if ($assuranceNode->definesStatic($assuredStaticName)) {
                // This context defines an assured static with the given name
                return $assuranceNode;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        $path = $this->parentContext->getPath();

        if ($this->actNode) {
            if ($path !== '') {
                $path .= '.';
            }

            $path .= '[' . $this->actNode->getType() . ']';
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariableType($variableName)
    {
        if (array_key_exists($variableName, $this->definedVariableTypes)) {
            return $this->definedVariableTypes[$variableName];
        }

        return $this->parentContext->getVariableType($variableName);
    }
}
