<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Evaluation;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Event\Evaluation\EventEvaluationContext;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Signal\Evaluation\SignalEvaluationContext;
use Combyna\Component\Signal\SignalInterface;

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
    public function createEventContext(
        EvaluationContextInterface $parentContext,
        EventInterface $event
    ) {
        return new EventEvaluationContext($this, $parentContext, $event);
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

    /**
     * {@inheritdoc}
     */
    public function createSignalContext(
        EvaluationContextInterface $parentContext,
        SignalInterface $signal
    ) {
        return new SignalEvaluationContext($this, $parentContext, $signal);
    }

    /**
     * {@inheritdoc}
     */
    public function getStateTypeToContextFactoryMap()
    {
        return [];
    }
}
