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
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Interface FixedStaticBagModelInterface
 *
 * Defines the static names and their types that a bag may store internally
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FixedStaticBagModelInterface
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
     * Returns true if this model defines a static with the specified name, false otherwise
     *
     * @param string $name
     * @return bool
     */
    public function definesStatic($name);

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
     * @return TypeInterface
     */
    public function getStaticType($name);
}
