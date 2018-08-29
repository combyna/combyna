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

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Component\Validator\Query\ActNodeQueryInterface;
use Combyna\Component\Validator\Query\BooleanQueryInterface;
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;
use Combyna\Component\Validator\ViolationInterface;

/**
 * Interface RootValidationContextInterface
 *
 * Represents a current state during validation, tracking any violations
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RootValidationContextInterface
{
    /**
     * Logs a validation constraint violation against this context
     * to indicate that an attempt would be made to divide by zero,
     * marking the validation as failed
     *
     * @param SubValidationContextInterface $subValidationContext
     */
    public function addDivisionByZeroViolation(SubValidationContextInterface $subValidationContext);

    /**
     * Logs a generic constraint violation against this context,
     * marking the validation as failed
     *
     * @param string $description
     * @param SubValidationContextInterface $subValidationContext
     */
    public function addGenericViolation($description, SubValidationContextInterface $subValidationContext);

    /**
     * Logs a type mismatch constraint violation against this context,
     * marking the validation as failed
     *
     * @param TypeInterface $expectedType
     * @param TypeInterface $actualType
     * @param string $contextDescription
     * @param SubValidationContextInterface $subValidationContext
     */
    public function addTypeMismatchViolation(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        $contextDescription,
        SubValidationContextInterface $subValidationContext
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
     * @param SubValidationContextInterface $subValidationContext
     */
    public function adoptDynamicActNode(DynamicActNodeInterface $actNode, SubValidationContextInterface $subValidationContext);

    /**
     * Fetches all descendant nodes of the specified behaviour spec that match the given query specifier
     *
     * @param QuerySpecifierInterface $querySpecifier
     * @param BehaviourSpecInterface $behaviourSpec
     * @return BehaviourSpecInterface[]
     */
    public function getDescendantSpecsWithQuery(
        QuerySpecifierInterface $querySpecifier,
        BehaviourSpecInterface $behaviourSpec
    );

    /**
     * Fetches the result type for the specified expression node,
     * which does not need to be in the ancestry of the current ACT node
     *
     * @param ExpressionNodeInterface $expressionNode
     * @return TypeInterface
     */
    public function getExpressionResultType(ExpressionNodeInterface $expressionNode);

    /**
     * Fetches the root sub-validation context
     *
     * @return RootSubValidationContextInterface
     */
    public function getRootSubValidationContext();

    /**
     * Performs a query that will result in an ACT node
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
     * @return TypeInterface
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
