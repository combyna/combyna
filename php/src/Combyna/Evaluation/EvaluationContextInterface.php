<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Evaluation;

use Combyna\Bag\StaticBagInterface;
use Combyna\Expression\ExpressionInterface;
use Combyna\Expression\StaticInterface;

/**
 * Interface EvaluationContextInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EvaluationContextInterface
{
    /**
     * Creates a new EvaluationContext as a child of the current one, with the specified expression
     * as the one to use as "current" and the provided static bag exposed inside it as variables
     *
     * @param ExpressionInterface $expression
     * @param StaticBagInterface|null $staticBag
     * @return EvaluationContextInterface
     */
    public function createSubContext(ExpressionInterface $expression, StaticBagInterface $staticBag = null);

    /**
     * Fetches the specified assured static value
     *
     * @param string $assuredStaticName
     * @return StaticInterface
     */
    public function getAssuredStatic($assuredStaticName);
}
