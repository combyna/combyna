<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Evaluation;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\ExpressionInterface;

/**
 * Interface EvaluationContextFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EvaluationContextFactoryInterface
{
    /**
     * Creates a new AssuredEvaluationContext
     *
     * @param EvaluationContextInterface $parentContext
     * @param StaticBagInterface $assuredStaticBag
     * @return AssuredEvaluationContext
     */
    public function createAssuredContext(
        EvaluationContextInterface $parentContext,
        StaticBagInterface $assuredStaticBag
    );

    /**
     * Creates a new ExpressionEvaluationContext
     *
     * @param EvaluationContextInterface $parentContext
     * @param ExpressionInterface $expression
     * @return ExpressionEvaluationContext
     */
    public function createExpressionContext(
        EvaluationContextInterface $parentContext,
        ExpressionInterface $expression
    );

    /**
     * Creates a new RootEvaluationContext
     *
     * @param EnvironmentInterface $environment
     * @return RootEvaluationContext
     */
    public function createRootContext(EnvironmentInterface $environment);

    /**
     * Creates a new ScopeEvaluationContext
     *
     * @param EvaluationContextInterface $parentContext
     * @param StaticBagInterface $variableStaticBag
     * @return ScopeEvaluationContext
     */
    public function createScopeContext(
        EvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag
    );
}
