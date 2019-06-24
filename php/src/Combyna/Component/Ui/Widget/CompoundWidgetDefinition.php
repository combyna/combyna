<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Widget;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Event\EventDefinitionReferenceCollectionInterface;
use Combyna\Component\Event\EventFactoryInterface;
use Combyna\Component\Event\Exception\EventDefinitionNotReferencedException;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\CompoundWidgetEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\DefinedWidgetEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\Event\Exception\EventDefinitionNotReferencedByWidgetException;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\Widget\DefinedCompoundWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use LogicException;

/**
 * Class CompoundWidgetDefinition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CompoundWidgetDefinition implements WidgetDefinitionInterface
{
    const TYPE = 'compound';

    /**
     * @var FixedStaticBagModelInterface
     */
    private $attributeBagModel;

    /**
     * @var EventDefinitionReferenceCollectionInterface
     */
    private $eventDefinitionReferenceCollection;

    /**
     * @var EventFactoryInterface
     */
    private $eventFactory;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var WidgetInterface
     */
    private $rootWidget;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @var ExpressionBagInterface
     */
    private $valueExpressionBag;

    /**
     * @param UiStateFactoryInterface $uiStateFactory
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     * @param EventFactoryInterface $eventFactory
     * @param EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection
     * @param string $libraryName
     * @param string $name
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @param ExpressionBagInterface $valueExpressionBag
     * @param WidgetInterface $rootWidget
     */
    public function __construct(
        UiStateFactoryInterface $uiStateFactory,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory,
        EventFactoryInterface $eventFactory,
        EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection,
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel,
        ExpressionBagInterface $valueExpressionBag,
        WidgetInterface $rootWidget
    ) {
        $this->attributeBagModel = $attributeBagModel;
        $this->eventDefinitionReferenceCollection = $eventDefinitionReferenceCollection;
        $this->eventFactory = $eventFactory;
        $this->libraryName = $libraryName;
        $this->name = $name;
        $this->rootWidget = $rootWidget;
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
        $this->uiStateFactory = $uiStateFactory;
        $this->valueExpressionBag = $valueExpressionBag;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidAttributeStaticBag(StaticBagInterface $attributeStaticBag)
    {
        $this->attributeBagModel->assertValidStaticBag($attributeStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createDefinitionEvaluationContextForWidget(
        DefinedWidgetEvaluationContextInterface $parentContext,
        DefinedWidgetInterface $widget,
        DefinedWidgetStateInterface $widgetState = null
    ) {
        if (!$parentContext instanceof CompoundWidgetEvaluationContextInterface) {
            throw new LogicException(
                sprintf(
                    'Expected %s, got %s',
                    CompoundWidgetEvaluationContextInterface::class,
                    get_class($parentContext)
                )
            );
        }

        return $this->uiEvaluationContextFactory
            ->createCompoundWidgetDefinitionEvaluationContext(
                $parentContext,
                $this,
                $widget,
                $widgetState
            );
    }

    /**
     * {@inheritdoc}
     */
    public function createEvaluationContextForWidget(
        ViewEvaluationContextInterface $parentContext,
        DefinedWidgetInterface $widget,
        DefinedWidgetStateInterface $widgetState = null
    ) {
        if ($widgetState && !$widgetState instanceof DefinedCompoundWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected %s, got %s',
                    DefinedCompoundWidgetStateInterface::class,
                    get_class($widgetState)
                )
            );
        }

        return $this->uiEvaluationContextFactory->createCompoundWidgetEvaluationContext(
            $parentContext,
            $this,
            $widget,
            $widgetState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createEvent(
        $libraryName,
        $eventName,
        array $payloadNatives,
        ViewEvaluationContextInterface $evaluationContext
    ) {
        try {
            $eventDefinition = $this->eventDefinitionReferenceCollection->getDefinitionByName($libraryName, $eventName);
        } catch (EventDefinitionNotReferencedException $exception) {
            throw new EventDefinitionNotReferencedByWidgetException(
                $libraryName,
                $eventName,
                $this->libraryName,
                $this->name
            );
        }

        $payloadStaticBag = $eventDefinition
            ->getPayloadStaticBagModel()
            ->coerceNativeArrayToBag($payloadNatives, $evaluationContext);

        return $this->eventFactory->createEvent($eventDefinition, $payloadStaticBag, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialStateForWidget(
        $name,
        DefinedWidgetInterface $widget,
        ExpressionBagInterface $attributeExpressionBag,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        // Create a sub-evaluation context for the compound widget itself,
        // so that its attributes/values may be fetched by expressions inside the root widget's structure
        // as well as its attributes and default widget-value values
        $widgetSubEvaluationContext = $this->createEvaluationContextForWidget(
            $evaluationContext,
            $widget
        );

        $childWidgetStates = [];

        foreach ($widget->getChildWidgets() as $childName => $childWidget) {
            $childWidgetStates[$childName] = $childWidget->createInitialState(
                $childName,
                // Child widgets only get the CompoundWidgetEvaluationContext
                // and not the *Definition one, as they do not have access to the attributes
                // and values of the compound widget
                $widgetSubEvaluationContext,
                $evaluationContextFactory
            );
        }

        $definitionSubEvaluationContext = $this->createDefinitionEvaluationContextForWidget(
            $widgetSubEvaluationContext,
            $widget
        );

        // Create the default state for the root widget that makes up the compound widget
        $rootWidgetState = $this->rootWidget->createInitialState(
            'root',
            $definitionSubEvaluationContext,
            $evaluationContextFactory
        );

        /*
         * Evaluate the expressions for the attributes of this widget -
         * attributes can reference values of this widget
         */
        $attributeStaticBag = $this->attributeBagModel->createBag(
            $attributeExpressionBag,
            $widgetSubEvaluationContext,
            $definitionSubEvaluationContext
        );

        /*
         * Evaluate the expressions for the values of this widget -
         * values can reference attributes of this widget
         */
        $valueStaticBag = $this->valueExpressionBag->toStaticBag(
            $definitionSubEvaluationContext
        );

        return $this->uiStateFactory->createDefinedCompoundWidgetState(
            $name,
            $widget,
            $attributeStaticBag,
            $valueStaticBag,
            $childWidgetStates,
            $rootWidgetState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(
        $name,
        ExpressionBagInterface $attributeExpressionBag,
        EvaluationContextInterface $evaluationContext
    ) {
        return $this->attributeBagModel->coerceStatic(
            $name,
            $evaluationContext,
            $attributeExpressionBag,
            $attributeExpressionBag->hasExpression($name) ?
                $attributeExpressionBag->getExpression($name)->toStatic($evaluationContext) :
                null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Fetches the root widget that defines the structure of this compound widget
     *
     * @return WidgetInterface
     */
    public function getRootWidget()
    {
        return $this->rootWidget;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetValue(
        $valueName,
        array $widgetStatePath,
        ViewEvaluationContextInterface $evaluationContext
    ) {
        return $this->valueExpressionBag->getExpression($valueName)->toStatic($evaluationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function isRenderable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function reevaluateStateForWidget(
        DefinedWidgetStateInterface $oldState,
        DefinedWidgetInterface $widget,
        ExpressionBagInterface $attributeExpressionBag,
        ViewEvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        if (!$oldState instanceof DefinedCompoundWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected %s, got %s',
                    DefinedCompoundWidgetStateInterface::class,
                    get_class($oldState)
                )
            );
        }

        // Create a sub-evaluation context for the compound widget itself,
        // so that its attributes/values may be fetched by expressions inside the root widget's structure
        // as well as its attributes and default widget-value values
        $widgetSubEvaluationContext = $this->createEvaluationContextForWidget(
            $evaluationContext,
            $widget,
            $oldState
        );

        $childWidgetStates = [];

        foreach ($widget->getChildWidgets() as $childName => $childWidget) {
            $childWidgetStates[$childName] = $childWidget->reevaluateState(
                $oldState->getChildState($childName),
                // Child widgets only get the CompoundWidgetEvaluationContext
                // and not the *Definition one, as they do not have access to the attributes
                // and values of the compound widget
                $widgetSubEvaluationContext,
                $evaluationContextFactory
            );
        }

        $definitionSubEvaluationContext = $this->createDefinitionEvaluationContextForWidget(
            $widgetSubEvaluationContext,
            $widget,
            $oldState
        );

        // Create the default state for the root widget that makes up the compound widget
        $rootWidgetState = $this->rootWidget->reevaluateState(
            $oldState->getRootWidgetState(),
            $definitionSubEvaluationContext,
            $evaluationContextFactory
        );

        /*
         * Evaluate the expressions for the attributes of this widget -
         * attributes can reference values of this widget
         */
        $attributeStaticBag = $this->attributeBagModel->createBag(
            $attributeExpressionBag,
            $widgetSubEvaluationContext,
            $definitionSubEvaluationContext
        );

        /*
         * Evaluate the expressions for the values of this widget -
         * values can reference attributes of this widget
         */
        $valueStaticBag = $this->valueExpressionBag->toStaticBag(
            $definitionSubEvaluationContext
        );

        return $oldState->with(
            $attributeStaticBag,
            $valueStaticBag,
            $childWidgetStates,
            $rootWidgetState
        );
    }
}
