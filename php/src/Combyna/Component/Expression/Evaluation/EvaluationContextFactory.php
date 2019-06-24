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

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Event\Evaluation\EventEvaluationContext;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Program\ResourceRepositoryInterface;
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
    public function createNullRootContext()
    {
        return new NullRootEvaluationContext($this);
    }

    /**
     * {@inheritdoc}
     */
    public function createRootContext(ResourceRepositoryInterface $resourceRepository)
    {
        return new RootEvaluationContext($this, $resourceRepository);
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
    public function getParentStateTypeToContextFactoryMap()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getStateTypeToContextFactoryMap()
    {
        return [];
    }
}
