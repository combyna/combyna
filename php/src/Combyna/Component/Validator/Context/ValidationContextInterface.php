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

use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Component\Validator\Query\ActNodeQueryInterface;
use Combyna\Component\Validator\Query\BooleanQueryInterface;
use Combyna\Component\Validator\Query\Requirement\ActNodeQueryRequirement;
use Combyna\Component\Validator\Query\Requirement\BooleanQueryRequirement;
use Combyna\Component\Validator\Query\Requirement\TypeQueryRequirement;
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;
use Combyna\Component\Validator\ViolationInterface;

/**
 * Interface ValidationContextInterface
 *
 * Represents a current state during validation, tracking any violations
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ValidationContextInterface
{
    /**
     * Logs a validation constraint violation against this context
     * to indicate that an attempt would be made to divide by zero,
     * marking the validation as failed
     */
    public function addDivisionByZeroViolation();

    /**
     * Logs a generic constraint violation against this context,
     * marking the validation as failed
     *
     * @param string $description
     */
    public function addGenericViolation($description);

    /**
     * Logs a type mismatch constraint violation against this context,
     * marking the validation as failed
     *
     * @param TypeInterface $expectedType
     * @param TypeInterface $actualType
     * @param string $contextDescription
     */
    public function addTypeMismatchViolation(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        $contextDescription
    );

    /**
     * Logs a validation constraint violation against this context,
     * marking the validation as failed
     *
     * @param ViolationInterface $violation
     */
    public function addViolation(ViolationInterface $violation);

    /**
     * Applies the validation for the provided dynamically-created ACT node
     *
     * @param DynamicActNodeInterface $actNode
     */
    public function adoptDynamicActNode(DynamicActNodeInterface $actNode);

    /**
     * Creates a new ActNodeQueryRequirement
     *
     * @param ActNodeQueryInterface $query
     * @param ActNodeInterface $nodeToQueryFrom
     * @return ActNodeQueryRequirement
     */
    public function createActNodeQueryRequirement(ActNodeQueryInterface $query, ActNodeInterface $nodeToQueryFrom);

    /**
     * Creates a new BooleanQueryRequirement
     *
     * @param BooleanQueryInterface $query
     * @return BooleanQueryRequirement
     */
    public function createBooleanQueryRequirement(BooleanQueryInterface $query);

    /**
     * Creates a child ValidationContext of the current one
     *
     * @param SubValidationContextSpecifierInterface $subContextSpecifier
     * @param StructuredNodeInterface $structuredNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @return ValidationContextInterface
     */
    public function createSubContext(
        SubValidationContextSpecifierInterface $subContextSpecifier,
        StructuredNodeInterface $structuredNode,
        BehaviourSpecInterface $behaviourSpec
    );

    /**
     * Creates a new TypeQueryRequirement
     *
     * @param ResultTypeQueryInterface $query
     * @return TypeQueryRequirement
     */
    public function createTypeQueryRequirement(ResultTypeQueryInterface $query);

    /**
     * Fetches the ACT node that the current context represents.
     * This is not necessarily the same as the original node being validated -
     * to fetch that, see ::getSubjectActNode()
     *
     * @return ActNodeInterface
     */
    public function getCurrentActNode();

    /**
     * Fetches the parent of the current ACT node that the current context represents.
     * This is not necessarily the same as the parent of the original node being validated -
     * to fetch that, see ::getSubjectActNode()
     *
     * @return ActNodeInterface
     */
    public function getCurrentParentActNode();

    /**
     * Fetches all descendant nodes of the current ACT node (which is not necessarily an expression node)
     * that perform a query matching the given query specifier
     *
     * @param QuerySpecifierInterface $querySpecifier
     * @return BehaviourSpecInterface[]
     */
    public function getDescendantSpecsWithQuery(QuerySpecifierInterface $querySpecifier);

    /**
     * Fetches the result type for the specified expression node
     *
     * @param ExpressionNodeInterface $expressionNode
     * @return TypeInterface
     */
    public function getExpressionResultType(ExpressionNodeInterface $expressionNode);

    /**
     * Builds the path to this validation context in the expression tree
     *
     * @return string
     */
    public function getPath();

    /**
     * Fetches the original ACT node currently being validated
     *
     * @return ActNodeInterface
     */
    public function getSubjectActNode();

    /**
     * Fetches the sub-validation context
     *
     * @return SubValidationContextInterface
     */
    public function getSubValidationContext();

    /**
     * Performs a query that will result in a boolean
     *
     * @param ActNodeQueryInterface $actNodeQuery
     * @param ActNodeInterface $nodeToQueryFrom
     * @return ActNodeInterface
     */
    public function queryForActNode(
        ActNodeQueryInterface $actNodeQuery,
        ActNodeInterface $nodeToQueryFrom
    );

    /**
     * Performs a query that will result in a boolean
     *
     * @param BooleanQueryInterface $booleanQuery
     * @param ActNodeInterface $nodeToQueryFrom
     * @return bool|null
     */
    public function queryForBoolean(
        BooleanQueryInterface $booleanQuery,
        ActNodeInterface $nodeToQueryFrom
    );

    /**
     * Performs a query that will result in a Type
     *
     * @param ResultTypeQueryInterface $resultTypeQuery
     * @param ActNodeInterface $nodeToQueryFrom
     * @return TypeInterface|null
     */
    public function queryForResultType(
        ResultTypeQueryInterface $resultTypeQuery,
        ActNodeInterface $nodeToQueryFrom
    );

    /**
     * Throws if any violations have been added to this context, does nothing otherwise
     *
     * @throws ValidationFailureException
     */
    public function throwIfViolated();
}
