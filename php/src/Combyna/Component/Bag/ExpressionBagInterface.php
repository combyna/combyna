<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;

/**
 * Interface ExpressionBagInterface
 *
 * Contains a collection of related expressions
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionBagInterface
{
    /**
     * Fetches the specified expression from this bag
     *
     * @param string $name
     * @return ExpressionInterface
     */
    public function getExpression($name);

    /**
     * Fetches the names of all expressions in this bag
     *
     * @return string[]
     */
    public function getExpressionNames();

    /**
     * Determines whether this bag contains an expression with the specified name
     *
     * @param string $name
     * @return bool
     */
    public function hasExpression($name);

    /**
     * Evaluates all expressions in this bag to statics and returns a new StaticBag containing them
     *
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticBagInterface
     */
    public function toStaticBag(EvaluationContextInterface $evaluationContext);
}
