<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Expression;

use Combyna\Evaluation\EvaluationContextInterface;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Type\TypeInterface;

/**
 * Interface ExpressionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionInterface
{
    const TYPE = 'expression';

    /**
     * Fetches the type this expression will evaluate to
     *
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function getResultType(ValidationContextInterface $validationContext);

    /**
     * Fetches the type of expression, eg. `text`
     *
     * @return string
     */
    public function getType();

    /**
     * Coerces this expression to a static
     *
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticInterface
     */
    public function toStatic(EvaluationContextInterface $evaluationContext);

    /**
     * Checks that all operands for this expression validate recursively and that they will only
     * resolve to the expected types of static expression
     *
     * @param ValidationContextInterface $validationContext
     */
    public function validate(ValidationContextInterface $validationContext);
}
