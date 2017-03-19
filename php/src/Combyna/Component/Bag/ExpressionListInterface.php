<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\TypeInterface;
use Countable;

/**
 * Interface ExpressionListInterface
 *
 * Contains a list of expressions
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionListInterface extends Countable
{
    /**
     * Returns a type that represents all possible return types for the elements in the list
     * (eg. if all elements could only evaluate to NumberExpressions,
     *      then this would return StaticType<NumberExpression>. If one element
     *      could evaluate to a TextExpression, then it would return
     *      MultipleType<NumberExpression, TextExpression>)
     *
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function getElementResultType(ValidationContextInterface $validationContext);

    /**
     * Evaluates all expressions in this list to static values
     *
     * @return StaticListInterface
     */
    public function toStaticList(EvaluationContextInterface $evaluationContext);

    /**
     * Validates all expressions in this list
     *
     * @param ValidationContextInterface $validationContext
     */
    public function validate(ValidationContextInterface $validationContext);
}
