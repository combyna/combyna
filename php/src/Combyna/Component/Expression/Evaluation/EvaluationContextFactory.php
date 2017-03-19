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

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\ExpressionInterface;

/**
 * Class EvaluationContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EvaluationContextFactory implements EvaluationContextFactoryInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @param BagFactoryInterface $bagFactory
     */
    public function __construct(BagFactoryInterface $bagFactory)
    {
        $this->bagFactory = $bagFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createAssuredContext(
        EvaluationContextInterface $parentContext,
        StaticBagInterface $assuredStaticBag
    ) {
        return new AssuredEvaluationContext($this, $parentContext, $assuredStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createExpressionContext(
        EvaluationContextInterface $parentContext,
        ExpressionInterface $expression
    ) {
        return new ExpressionEvaluationContext($this, $parentContext, $expression);
    }

    /**
     * {@inheritdoc}
     */
    public function createRootContext(EnvironmentInterface $environment)
    {
        return new RootEvaluationContext($this, $environment);
    }

    /**
     * {@inheritdoc}
     */
    public function createScopeContext(
        EvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag
    ) {
        return new ScopeEvaluationContext($this, $parentContext, $variableStaticBag);
    }
}
