<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Assurance;

use Combyna\Component\Bag\MutableStaticBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

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
     * @param MutableStaticBagInterface $staticBag
     * @return bool
     */
    public function evaluate(EvaluationContextInterface $evaluationContext, MutableStaticBagInterface $staticBag);

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
}
