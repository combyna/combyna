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
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Ui\Exception\NotInsideCompoundWidgetDefinitionException;
use LogicException;

/**
 * Class RootEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RootEvaluationContext implements EvaluationContextInterface
{
    /**
     * @var EvaluationContextFactoryInterface
     */
    private $evaluationContextFactory;

    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param ResourceRepositoryInterface $resourceRepository
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        ResourceRepositoryInterface $resourceRepository
    ) {
        $this->evaluationContextFactory = $evaluationContextFactory;
        $this->resourceRepository = $resourceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildRouteUrl($libraryName, $routeName, StaticBagInterface $argumentStaticBag)
    {
        $route = $this->resourceRepository->getRouteByName($libraryName, $routeName);

        return $route->generateUrl($argumentStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function callFunction(
        $libraryName,
        $functionName,
        StaticBagInterface $argumentStaticBag,
        TypeInterface $returnType
    ) {
        // TODO: Support app-defined (non-native) functions
        $function = $this->resourceRepository
            ->getEnvironment()
            ->getGenericFunctionByName($libraryName, $functionName);

        return $function->call($argumentStaticBag, $returnType);
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
    public function getCaptureLeafwise($captureName)
    {
        throw new LogicException('Root evaluation context cannot set capture "' . $captureName . '"');
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureRootwise($captureName)
    {
        throw new LogicException('No capture is defined with name "' . $captureName . '"');
    }

    /**
     * {@inheritdoc}
     */
    public function getCompoundWidgetDefinitionContext()
    {
        throw new NotInsideCompoundWidgetDefinitionException(
            'Compound widget definition context cannot be fetched outside a widget'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment()
    {
        return $this->resourceRepository->getEnvironment();
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
    public function getParentContext()
    {
        return null; // Root context has no parent
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute($libraryName, $routeName)
    {
        return $this->resourceRepository->getRouteByName($libraryName, $routeName);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteArgument($parameterName)
    {
        throw new LogicException('Route argument "' . $parameterName . '" cannot be fetched outside a view');
    }

    /**
     * {@inheritdoc}
     */
    public function getSiblingBagStatic($staticName)
    {
        throw new LogicException(
            sprintf(
                'Sibling bag static "%s" cannot be fetched outside a bag',
                $staticName
            )
        );
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
    public function getWidgetValue($valueName)
    {
        throw new LogicException(
            'Value "' . $valueName . '" cannot be fetched outside a defined widget'
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
        return $this->resourceRepository->translate($translationKey, $parameters);
    }
}
