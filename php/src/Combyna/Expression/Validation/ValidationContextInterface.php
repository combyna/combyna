<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Expression\Validation;

use Combyna\Expression\Assurance\AssuranceInterface;
use Combyna\Expression\ExpressionInterface;
use Combyna\Expression\Validation\Exception\ValidationFailureException;
use Combyna\Type\TypeInterface;

/**
 * Class ValidationContextInterface
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
     * Logs a validation constraint violation against this context,
     * marking the validation as failed
     *
     * @param ViolationInterface $violation
     */
    public function addViolation(ViolationInterface $violation);

    /**
     * Checks that the expression is an AssuredExpression, and that the assured static
     * it refers to has the specified constraint
     *
     * @param ExpressionInterface $expression
     * @param string $constraint
     * @param string $contextDescription A description of the context: eg. 'left operand'
     */
    public function assertAssured(
        ExpressionInterface $expression,
        $constraint,
        $contextDescription
    );

    /**
     * Checks that an assured static exists with the given name in the hierarchy,
     *
     * @param string $assuredStaticName
     * @param ValidationContextInterface $currentValidationContext
     */
    public function assertAssuredStaticExists(
        $assuredStaticName,
        ValidationContextInterface $currentValidationContext
    );

    /**
     * Checks that the specified expression can only ever evaluate to match the static type
     * specified. If the expression is able to evaluate to a static type that doesn't match,
     * then a validation violation will be logged
     *
     * @param ExpressionInterface $expression
     * @param TypeInterface $allowedType Type allowed for the expression to evaluate to
     * @param string $contextDescription A description of the context: eg. 'left operand'
     */
    public function assertResultType(
        ExpressionInterface $expression,
        TypeInterface $allowedType,
        $contextDescription
    );

    /**
     * Creates a sub-context of this one that is aware of the current expression,
     * so that any failures may be mapped to the correct expression in the tree
     *
     * @return ValidationContextInterface
     */
    public function createSubContext(ExpressionInterface $expression);

    /**
     * Fetches the assurance for an assured static
     *
     * @param string $assuredStaticName
     * @return AssuranceInterface
     */
    public function getAssuredStaticAssurance($assuredStaticName);

    /**
     * Fetches the type for an assured static
     *
     * @param string $assuredStaticName
     * @return TypeInterface
     */
    public function getAssuredStaticType($assuredStaticName);

    /**
     * Builds the path to this validation context in the expression tree
     *
     * @return string
     */
    public function getPath();

    /**
     * Throws if any violations have been added to this context, does nothing otherwise
     *
     * @throws ValidationFailureException
     */
    public function throwIfViolated();
}
