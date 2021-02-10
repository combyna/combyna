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

use Combyna\Component\Bag\Config\Act\DeterminedFixedStaticDefinitionInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticInterface;
use LogicException;

/**
 * Interface FixedStaticDefinitionInterface
 *
 * Defines the name, type and default static value for a static in a bag
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FixedStaticDefinitionInterface extends DeterminedFixedStaticDefinitionInterface
{
    /**
     * Determines whether the type for this definition allows the specified static
     *
     * @param StaticInterface $static
     * @return bool
     */
    public function allowsStatic(StaticInterface $static);

    /**
     * Given a static value for this defined static:
     * - If a valid "complete" value for the static, the value is returned unmodified
     * - If a valid but "incomplete" value for the static, eg. a structure missing some optional attributes,
     *   the missing attributes will be added and a new, "complete" value returned
     * - If null is provided rather than a static, the default value will be evaluated and returned
     *
     * @param EvaluationContextInterface $evaluationContext
     * @param StaticInterface|null $static
     * @return StaticInterface
     */
    public function coerceStatic(EvaluationContextInterface $evaluationContext, StaticInterface $static = null);

    /**
     * Fetches the default value for this static, if configured
     *
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticInterface
     * @throws LogicException when no default static has been configured
     */
    public function getDefaultStatic(EvaluationContextInterface $evaluationContext);
}
