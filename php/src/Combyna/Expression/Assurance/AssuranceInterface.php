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

use Combyna\Evaluation\EvaluationContextInterface;
use Combyna\Expression\StaticInterface;
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
     * Fetches the constraint for this assurance type (one of the constants)
     *
     * @return string
     */
    public function getConstraint();

    /**
     * Fetches the unique name for this assured value
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the type that this static must evaluate to
     *
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function getType(ValidationContextInterface $validationContext);

    /**
     * Evaluates this assurance to a static result. If it meets its constraint
     * then it will be returned, otherwise null
     *
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticInterface|null
     */
    public function toStatic(EvaluationContextInterface $evaluationContext);

    /**
     * Checks that all operands for this assurance validate recursively and that they will only
     * resolve to the expected types of static expression
     *
     * @param ValidationContextInterface $validationContext
     */
    public function validate(ValidationContextInterface $validationContext);
}
