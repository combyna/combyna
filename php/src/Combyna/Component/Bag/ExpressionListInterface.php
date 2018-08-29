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
     * Evaluates all expressions in this list to static values
     *
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticListInterface
     */
    public function toStaticList(EvaluationContextInterface $evaluationContext);
}
