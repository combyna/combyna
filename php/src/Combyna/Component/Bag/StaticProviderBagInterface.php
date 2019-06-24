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

use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticInterface;

/**
 * Interface StaticProviderBagInterface
 *
 * Represents a bag that either contains evaluated statics or contains expressions
 * that may be evaluated in order to determine their static values
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StaticProviderBagInterface
{
    /**
     * Fetches/evaluates the specified static, if possible
     *
     * @param string $staticName
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticInterface
     * @throws NotFoundException
     */
    public function evaluateStatic($staticName, EvaluationContextInterface $evaluationContext);

    /**
     * Determines whether this provider can provide the specified static
     *
     * @param string $staticName
     * @return bool
     */
    public function providesStatic($staticName);
}
