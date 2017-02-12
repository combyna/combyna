<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Expression\Assurance;

use Combyna\Bag\StaticBagInterface;
use Combyna\Evaluation\EvaluationContextInterface;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Type\TypeInterface;

/**
 * Interface AssuranceInterface
 *
 * Defines a type and constraint that an expression must evaluate to.
 * At compile-time, the expression is validated to ensure it can only resolve to the correct type.
 * When evaluated at run-time, the static result is checked to ensure it evaluates to a value
 * expected by the constraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AssuranceInterface
{
    const NON_ZERO_NUMBER = 'non-zero-number';

    /**
     * Determines whether this assurance defines a static with the given name
     *
     * @param string $staticName
     * @return bool
     */
    public function definesStatic($staticName);

    /**
     * Evaluates this assurance to a set of zero or more static results.
     * If it meets its constraint then it will return true, otherwise false
     *
     * @param EvaluationContextInterface $evaluationContext
     * @param StaticBagInterface $staticBag
     * @return bool
     */
    public function evaluate(EvaluationContextInterface $evaluationContext, StaticBagInterface $staticBag);

    /**
     * Fetches the constraint for this assurance type (one of the constants)
     *
     * @return string
     */
    public function getConstraint();

    /**
     * Fetches the names of any and all assured statics that this assurance will define
     * that must be referenced by an AssuredExpression
     *
     * @return string[]
     */
    public function getRequiredAssuredStaticNames();

    /**
     * Fetches the type that a static this assurance defines must evaluate to
     *
     * @param ValidationContextInterface $validationContext
     * @param string $assuredStaticName
     * @return TypeInterface
     */
    public function getStaticType(ValidationContextInterface $validationContext, $assuredStaticName);

    /**
     * Checks that all operands for this assurance validate recursively and that they will only
     * resolve to the expected types of static expression
     *
     * @param ValidationContextInterface $validationContext
     */
    public function validate(ValidationContextInterface $validationContext);
}
