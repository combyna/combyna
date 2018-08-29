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
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Signal\SignalInterface;
use LogicException;

/**
 * Class RootEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RootEvaluationContext implements EvaluationContextInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @var EvaluationContextFactoryInterface
     */
    private $evaluationContextFactory;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EnvironmentInterface $environment
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EnvironmentInterface $environment
    ) {
        $this->environment = $environment;
        $this->evaluationContextFactory = $evaluationContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function callFunction($libraryName, $functionName, StaticBagInterface $argumentStaticBag)
    {
        $function = $this->environment->getGenericFunctionByName($libraryName, $functionName);

        return $function->call($argumentStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubAssuredContext(StaticBagInterface $assuredStaticBag)
    {
        return $this->evaluationContextFactory->createAssuredContext($this, $assuredStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubEventEvaluationContext(EventInterface $event)
    {
        return $this->evaluationContextFactory->createEventContext($this, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubExpressionContext(ExpressionInterface $expression)
    {
        return $this->evaluationContextFactory->createExpressionContext($this, $expression);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubScopeContext(StaticBagInterface $variableStaticBag)
    {
        return $this->evaluationContextFactory->createScopeContext($this, $variableStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubSignalEvaluationContext(SignalInterface $signal)
    {
        return $this->evaluationContextFactory->createSignalContext($this, $signal);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStatic($assuredStaticName)
    {
        throw new LogicException('No assured static is defined with name "' . $assuredStaticName . '"');
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventPayloadStatic($staticName)
    {
        throw new LogicException('Event payload static "' . $staticName . '" cannot be fetched outside a trigger');
    }

    /**
     * {@inheritdoc}
     */
    public function getSignalPayloadStatic($staticName)
    {
        throw new LogicException(
            'Signal payload static "' . $staticName .
            '" cannot be fetched outside a signal handler'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreSlotStatic($name)
    {
        throw new LogicException('Store slot "' . $name . '" cannot be fetched outside a store');
    }

    /**
     * {@inheritdoc}
     */
    public function getVariable($variableName)
    {
        throw new LogicException('No variable is defined with name "' . $variableName . '"');
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetAttribute($attributeName)
    {
        throw new LogicException(
            'Attribute "' . $attributeName . '" cannot be fetched outside a compound widget definition'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function makeViewStoreQuery($queryName, StaticBagInterface $argumentStaticBag)
    {
        throw new LogicException('No active store - cannot make query with name "' . $queryName . '"');
    }

    /**
     * {@inheritdoc}
     */
    public function translate($translationKey, array $parameters = [])
    {
        return $this->environment->translate($translationKey, $parameters);
    }
}
