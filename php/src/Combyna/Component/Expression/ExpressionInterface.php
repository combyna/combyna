<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Interface ExpressionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionInterface
{
    const TYPE = 'expression';

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
}
