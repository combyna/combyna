<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Component\Validator\ValidationFactoryInterface;
use Combyna\Component\Validator\ViolationInterface;
use LogicException;

/**
 * Class RootGenericValidationContext
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RootGenericValidationContext implements RootGenericValidationContextInterface
{
    /**
     * @var EnvironmentNode
     */
    private $environmentNode;

    /**
     * @var ValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @var ViolationInterface[]
     */
    private $violations = [];

    /**
     * @param ValidationFactoryInterface $validationFactory
     * @param EnvironmentNode $environmentNode
     */
    public function __construct(
        ValidationFactoryInterface $validationFactory,
        EnvironmentNode $environmentNode
    ) {
        $this->environmentNode = $environmentNode;
        $this->validationFactory = $validationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function addDivisionByZeroViolation(ValidationContextInterface $currentContext)
    {
        $this->addViolation($this->validationFactory->createDivisionByZeroViolation($currentContext));
    }

    /**
     * {@inheritdoc}
     */
    public function addGenericViolation($description, ValidationContextInterface $currentContext)
    {
        $this->addViolation(
            $this->validationFactory->createGenericViolation(
                $description,
                $currentContext
            )
        );
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
        $this->addViolation(
            $this->validationFactory->createTypeMismatchViolation(
                $expectedType,
                $actualType,
                $currentContext,
                $contextDescription
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addViolation(ViolationInterface $violation)
    {
        $this->violations[] = $violation;
    }

    /**
     * {@inheritdoc}
     */
    public function assertAllRequiredAssuredStaticsWereUsed(
        ValidationContextInterface $currentContext
    ) {
        $this->addViolation($this->validationFactory->createGenericViolation(
            'Not inside a GuardExpression',
            $currentContext
        ));
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
        $this->addViolation($this->validationFactory->createGenericViolation(
            $contextDescription . ' is not inside a GuardExpression',
            $currentContext
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function assertAssuredStaticExists(
        $assuredStaticName,
        ValidationContextInterface $currentContext
    ) {
        $this->addViolation($this->validationFactory->createGenericViolation(
            'No assured static has been defined with name "' . $assuredStaticName . '"',
            $currentContext
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function assertListResultType(
        ExpressionNodeInterface $expressionNode,
        $contextDescription,
        ValidationContextInterface $currentContext
    ) {
        $resultType = $expressionNode->getResultType($currentContext);

        if ($resultType instanceof StaticListType) {
            return;
        }

        $this->addViolation($this->validationFactory->createGenericViolation(
            $contextDescription . ' does not evaluate to a static list, it evaluates to ' . $resultType->getSummary(),
            $currentContext
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function assertOperator(
        $operator,
        array $allowedOperators,
        ValidationContextInterface $currentContext
    ) {
        if (!in_array($operator, $allowedOperators, true)) {
            $this->addViolation($this->validationFactory->createGenericViolation(
                'Operator "' . $operator . '" is not allowed, allowed operators are "' .
                implode('", "', $allowedOperators)
                . '"',
                $currentContext
            ));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param TypeInterface[] $allowedMatchingResultTypes
     */
    public function assertPossibleMatchingResultTypes(
        ExpressionNodeInterface $leftOperandExpressionNode,
        $leftOperandContextDescription,
        ExpressionNodeInterface $rightOperandExpressionNode,
        $rightOperandContextDescription,
        array $allowedMatchingResultTypes,
        ValidationContextInterface $currentContext
    ) {
        $leftOperandCandidateResultType = $leftOperandExpressionNode->getResultType($currentContext);
        $rightOperandCandidateResultType = $rightOperandExpressionNode->getResultType($currentContext);

        foreach ($allowedMatchingResultTypes as $allowedMatchingResultType) {
            if (
                $allowedMatchingResultType->allows($leftOperandCandidateResultType) &&
                $allowedMatchingResultType->allows($rightOperandCandidateResultType)
            ) {
                // Both operands' expressions are allowed by this possible result type
                return;
            }
        }

        $this->addViolation($this->validationFactory->createGenericViolation(
            'operands "' . $leftOperandCandidateResultType->getSummary() .
            '" and "' . $rightOperandCandidateResultType->getSummary() .
            '" do not both match just one of the provided allowed result types, ' .
            'allowed types are: "' .
            implode(
                '", "',
                array_map(
                    function (TypeInterface $allowedMatchingResultType) {
                        return $allowedMatchingResultType->getSummary();
                    },
                    $allowedMatchingResultTypes
                )
            )
            . '"',
            $currentContext
        ));
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
        $candidateResultType = $expressionNode->getResultType($currentContext);

        if (!$allowedType->allows($candidateResultType)) {
            $this->addViolation($this->validationFactory->createTypeMismatchViolation(
                $allowedType,
                $candidateResultType,
                $currentContext,
                $contextDescription
            ));
        }
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
        // May return an UnknownFunctionNode or UnknownLibraryAndFunctionNode if invalid,
        // but those nodes will make sure that validation fails
        $functionNode = $this->environmentNode->getGenericFunction($libraryName, $functionName);

        $functionNode->validateArgumentExpressionBag($currentContext, $argumentExpressionBagNode);
    }

    /**
     * {@inheritdoc}
     */
    public function assertVariableExists(
        $variableName,
        ValidationContextInterface $currentContext
    ) {
        $this->addViolation($this->validationFactory->createGenericViolation(
            'No variable has been defined with name "' . $variableName . '"',
            $currentContext
        ));
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
        // Only RootValidationContext(Interface) should wrap this class,
        // so this method should never be called
        throw new LogicException(
            'Root validation context cannot have any variables defined'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStaticAssurance(
        $assuredStaticName,
        ValidationContextInterface $currentContext
    ) {
        throw new LogicException(
            'No assured static is defined with name "' . $assuredStaticName . '"'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStaticType(
        $assuredStaticName,
        ValidationContextInterface $currentContext
    ) {
        throw new LogicException(
            'No assured static is defined with name "' . $assuredStaticName . '"'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctionReturnType(
        $libraryName,
        $functionName,
        ValidationContextInterface $currentContext
    ) {
        // May return an UnknownType if the library or function are not defined,
        // but assertions elsewhere will ensure that validation fails with the specific reason
        // so we don't need to worry about that here
        return $this->environmentNode->getGenericFunction($libraryName, $functionName)->getReturnType();
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnAssurance($assuredStaticName)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getVariableType($variableName)
    {
        throw new LogicException('No variable is defined with name "' . $variableName . '"');
    }

    /**
     * {@inheritdoc}
     */
    public function throwIfViolated()
    {
        if (count($this->violations) > 0) {
            $descriptions = [];

            foreach ($this->violations as $violation) {
                $descriptions[] = 'Expression ' . $violation->getPath() . ' - ' . $violation->getDescription();
            }

            throw new ValidationFailureException($this, implode('. :: ', $descriptions));
        }
    }
}
