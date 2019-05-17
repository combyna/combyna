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

use Combyna\Component\Bag\Config\Act\DeterminedFixedStaticBagModelInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Interface FixedStaticBagModelInterface
 *
 * Defines the static names and their types that a bag may store internally
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FixedStaticBagModelInterface extends DeterminedFixedStaticBagModelInterface
{
    /**
     * Checks that the static is defined and matches its type for this model
     *
     * @param string $name
     * @param StaticInterface $value
     */
    public function assertValidStatic($name, StaticInterface $value);

    /**
     * Checks that all statics in the provided bag are defined and match their types for this model
     *
     * @param StaticBagInterface $staticBag
     */
    public function assertValidStaticBag(StaticBagInterface $staticBag);

    /**
     * Given a static value for a defined static of this model:
     * - If a valid "complete" value for the static, the value is returned unmodified
     * - If a valid but "incomplete" value for the static, eg. a structure missing some optional attributes,
     *   the missing attributes will be added and a new, "complete" value returned
     * - If null is provided rather than a static, the default value will be evaluated and returned
     *
     * @param string $name
     * @param EvaluationContextInterface $evaluationContext
     * @param StaticInterface|null $static
     * @return StaticInterface
     */
    public function coerceStatic($name, EvaluationContextInterface $evaluationContext, StaticInterface $static = null);

    /**
     * Given a static bag for this model:
     * - If the bag is "complete" (with all statics contained inside and each "complete" recursively),
     *   the value is returned unmodified
     * - If the bag is "incomplete" (with some or all statics missing or "incomplete"), the missing/incomplete statics
     *   will be evaluated or coerced as per the statics' definitions.
     *
     * @param StaticBagInterface $staticBag
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticBagInterface
     */
    public function coerceStaticBag(StaticBagInterface $staticBag, EvaluationContextInterface $evaluationContext);

    /**
     * Creates a new StaticBag from a set of statics that is guaranteed to meet this fixed model.
     * Any optional statics in the model (those which have a default expression set) are missing
     * from the statics array provided, their default expressions will be evaluated to give their values.
     * The default expressions may be evaluated using a different evaluation context.
     *
     * @param ExpressionBagInterface $expressionBag
     * @param EvaluationContextInterface $explicitEvaluationContext
     * @param EvaluationContextInterface $defaultsEvaluationContext
     * @return StaticBagInterface
     */
    public function createBag(
        ExpressionBagInterface $expressionBag,
        EvaluationContextInterface $explicitEvaluationContext,
        EvaluationContextInterface $defaultsEvaluationContext
    );

    /**
     * Creates a new StaticBag from a callback that retrieves statics that is guaranteed to meet this fixed model
     *
     * @param callable $staticFetcher
     * @return StaticBagInterface
     */
    public function createBagWithCallback(callable $staticFetcher);

    /**
     * Creates a StaticBag with all the default statics of definitions in this model
     *
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticBagInterface
     */
    public function createDefaultStaticBag(EvaluationContextInterface $evaluationContext);

    /**
     * Evaluates and returns the default expression for the specified static of the model
     *
     * @param string $name
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticInterface
     */
    public function getDefaultStatic($name, EvaluationContextInterface $evaluationContext);

    /**
     * Fetches the type of the specified static in the model
     *
     * @param string $name
     * @return TypeInterface
     */
    public function getStaticType($name);
}
