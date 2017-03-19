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

use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Expression\Config\Act\Assurance\AssuranceNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Validator\ViolationInterface;
use Combyna\Component\Type\TypeInterface;
use LogicException;

/**
 * Interface GenericValidationContextInterface
 *
 * Handles the work for all types of specific validation context
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface GenericValidationContextInterface
{
    /**
     * Logs a validation constraint violation against this context
     * to indicate that an attempt would be made to divide by zero,
     * marking the validation as failed
     *
     * @param ValidationContextInterface $currentContext
     */
    public function addDivisionByZeroViolation(ValidationContextInterface $currentContext);

    /**
     * Logs a generic constraint violation against this context,
     * marking the validation as failed
     *
     * @param string $description
     * @param ValidationContextInterface $currentContext
     */
    public function addGenericViolation($description, ValidationContextInterface $currentContext);

    /**
     * Logs a type mismatch constraint violation against this context,
     * marking the validation as failed
     *
     * @param TypeInterface $expectedType
     * @param TypeInterface $actualType
     * @param string $contextDescription
     * @param ValidationContextInterface $currentContext
     */
    public function addTypeMismatchViolation(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        $contextDescription,
        ValidationContextInterface $currentContext
    );


    /**
     * Logs a validation constraint violation against this context,
     * marking the validation as failed
     *
     * @param ViolationInterface $violation
     */
    public function addViolation(ViolationInterface $violation);

    /**
     * Goes through all assurances in this assured context and checks that for each of them,
     * any of their assured statics that are "required" (and so must be used) have been
     * referenced by at least one AssuredExpression via ::assertAssuredStaticExists(...)
     *
     * @param ValidationContextInterface $currentContext
     */
    public function assertAllRequiredAssuredStaticsWereUsed(
        ValidationContextInterface $currentContext
    );

    /**
     * Checks that the expression is an AssuredExpression, and that the assured static
     * it refers to has the specified constraint
     *
     * @param ExpressionNodeInterface $expressionNode
     * @param string $constraint
     * @param string $contextDescription A description of the context: eg. 'left operand'
     * @param ValidationContextInterface $currentContext
     */
    public function assertAssured(
        ExpressionNodeInterface $expressionNode,
        $constraint,
        $contextDescription,
        ValidationContextInterface $currentContext
    );

    /**
     * Checks that an assured static exists with the given name in the hierarchy
     *
     * @param string $assuredStaticName
     * @param ValidationContextInterface $currentContext
     */
    public function assertAssuredStaticExists(
        $assuredStaticName,
        ValidationContextInterface $currentContext
    );

    /**
     * Checks that the expression resolves to a StaticListExpression
     * (the type of its elements is ignored)
     *
     * @param ExpressionNodeInterface $expressionNode
     * @param string $contextDescription
     * @param ValidationContextInterface $currentContext
     */
    public function assertListResultType(
        ExpressionNodeInterface $expressionNode,
        $contextDescription,
        ValidationContextInterface $currentContext
    );

    /**
     * Checks that the provided operator is included in the allowed set
     *
     * @param string $operator
     * @param string[] $allowedOperators
     * @param ValidationContextInterface $currentContext
     */
    public function assertOperator(
        $operator,
        array $allowedOperators,
        ValidationContextInterface $currentContext
    );

    /**
     * Checks that both of the specified expressions can both only ever evaluate to match
     * one of the provided static types together. If the expressions are only able to evaluate
     * to a static type that doesn't match, then a validation violation will be logged
     *
     * @param ExpressionNodeInterface $leftOperandExpressionNode
     * @param $leftOperandContextDescription
     * @param ExpressionNodeInterface $rightOperandExpressionNode
     * @param $rightOperandContextDescription
     * @param TypeInterface[] $allowedMatchingResultTypes
     * @param ValidationContextInterface $currentContext
     */
    public function assertPossibleMatchingResultTypes(
        ExpressionNodeInterface $leftOperandExpressionNode,
        $leftOperandContextDescription,
        ExpressionNodeInterface $rightOperandExpressionNode,
        $rightOperandContextDescription,
        array $allowedMatchingResultTypes,
        ValidationContextInterface $currentContext
    );

    /**
     * Checks that the specified expression can only ever evaluate to match the static type
     * specified. If the expression is able to evaluate to a static type that doesn't match,
     * then a validation violation will be logged
     *
     * @param ExpressionNodeInterface $expressionNode
     * @param TypeInterface $allowedType Type allowed for the expression to evaluate to
     * @param string $contextDescription A description of the context: eg. 'left operand'
     * @param ValidationContextInterface $currentContext
     */
    public function assertResultType(
        ExpressionNodeInterface $expressionNode,
        TypeInterface $allowedType,
        $contextDescription,
        ValidationContextInterface $currentContext
    );

    /**
     * Validates that the provided function exists and that the argument expressions
     * evaluate to valid statics for that function's parameters. At the root level
     * this will just check for a GenericFunction, but deeper validation contexts
     * will allow the more specific types of function (eg. ViewStoreFunction) too
     *
     * @param string $libraryName
     * @param string $functionName
     * @param ExpressionBagNode $argumentExpressionBagNode
     * @param ValidationContextInterface $currentContext
     */
    public function assertValidFunctionCall(
        $libraryName,
        $functionName,
        ExpressionBagNode $argumentExpressionBagNode,
        ValidationContextInterface $currentContext
    );

    /**
     * Checks that a variable exists with the given name in the hierarchy
     *
     * @param string $variableName
     * @param ValidationContextInterface $currentContext
     */
    public function assertVariableExists(
        $variableName,
        ValidationContextInterface $currentContext
    );

    /**
     * Creates a sub-assured context
     *
     * @param AssuranceNodeInterface[] $assuranceNodes
     * @return AssuredValidationContextInterface
     */
    public function createSubAssuredContext(array $assuranceNodes);

    /**
     * Creates a sub-context of this one that is aware of the current ACT node,
     * so that any failures may be mapped to the correct node in the tree
     *
     * @param ActNodeInterface $actNode
     * @return ActNodeValidationContextInterface
     */
    public function createSubActNodeContext(ActNodeInterface $actNode);

    /**
     * Creates a sub-context of this one that can have variables defined in its scope
     *
     * @return ScopeValidationContextInterface
     */
    public function createSubScopeContext();

    /**
     * Defines a variable that will exist in this context at run-time.
     * A violation will be added if any parent context already defines a variable
     * with the specified name, to prevent any confusion caused by shadowing
     *
     * @param string $variableName
     * @param TypeInterface $type
     */
    public function defineVariable($variableName, TypeInterface $type);

    /**
     * Fetches the assurance for an assured static
     *
     * @param string $assuredStaticName
     * @param ValidationContextInterface $currentContext
     * @return AssuranceNodeInterface
     */
    public function getAssuredStaticAssurance(
        $assuredStaticName,
        ValidationContextInterface $currentContext
    );

    /**
     * Fetches the type for an assured static
     *
     * @param string $assuredStaticName
     * @param ValidationContextInterface $currentContext
     * @return TypeInterface
     * @throws LogicException Throws when no assured static is defined with the given type
     */
    public function getAssuredStaticType(
        $assuredStaticName,
        ValidationContextInterface $currentContext
    );

    /**
     * Fetches the return type for a function of the correct type for the current context if defined
     *
     * @param string $libraryName
     * @param string $functionName
     * @param ValidationContextInterface $currentContext
     * @return TypeInterface
     */
    public function getFunctionReturnType(
        $libraryName,
        $functionName,
        ValidationContextInterface $currentContext
    );

    /**
     * Fetches an assurance with the given name if defined for the current (and not an ancestor) context,
     * otherwise returns null
     *
     * @param string $assuredStaticName
     * @return AssuranceNodeInterface|null
     */
    public function getOwnAssurance($assuredStaticName);

    /**
     * Builds the path to this validation context in the expression tree
     *
     * @return string
     */
    public function getPath();

    /**
     * Fetches the type for a variable defined in this context
     *
     * @param string $variableName
     * @return TypeInterface
     * @throws LogicException Throws when no assured static is defined with the given type
     */
    public function getVariableType($variableName);
}
