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

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Event\EventDefinitionReferenceCollectionInterface;
use Combyna\Component\Event\EventFactoryInterface;
use Combyna\Component\Event\Exception\EventDefinitionNotReferencedException;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Ui\Evaluation\DefinedWidgetEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\PrimitiveWidgetEvaluationContextInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\Event\Exception\EventDefinitionNotReferencedByWidgetException;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\Widget\DefinedPrimitiveWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use LogicException;

/**
 * Class PrimitiveWidgetDefinition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PrimitiveWidgetDefinition implements WidgetDefinitionInterface
{
    const TYPE = 'primitive';

    /**
     * @var FixedStaticBagModelInterface
     */
    private $attributeBagModel;

    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

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
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $valueBagModel;

    /**
     * @var callable[]
     */
    private $valueNameToProviderCallableMap;

    /**
     * @param BagFactoryInterface $bagFactory
     * @param UiStateFactoryInterface $uiStateFactory
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     * @param EventFactoryInterface $eventFactory
     * @param EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection
     * @param string $libraryName
     * @param string $name
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @param FixedStaticBagModelInterface $valueBagModel
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param callable[] $valueNameToProviderCallableMap
     */
    public function __construct(
        BagFactoryInterface $bagFactory,
        UiStateFactoryInterface $uiStateFactory,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory,
        EventFactoryInterface $eventFactory,
        EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection,
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel,
        FixedStaticBagModelInterface $valueBagModel,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        array $valueNameToProviderCallableMap
    ) {
        $this->attributeBagModel = $attributeBagModel;
        $this->bagFactory = $bagFactory;
        $this->eventDefinitionReferenceCollection = $eventDefinitionReferenceCollection;
        $this->eventFactory = $eventFactory;
        $this->libraryName = $libraryName;
        $this->name = $name;
        $this->staticExpressionFactory = $staticExpressionFactory;
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
        $this->uiStateFactory = $uiStateFactory;
        $this->valueBagModel = $valueBagModel;
        $this->valueNameToProviderCallableMap = $valueNameToProviderCallableMap;
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
        if (!$parentContext instanceof PrimitiveWidgetEvaluationContextInterface) {
            throw new LogicException(
                sprintf(
                    'Expected %s, got %s',
                    PrimitiveWidgetEvaluationContextInterface::class,
                    get_class($parentContext)
                )
            );
        }

        return $this->uiEvaluationContextFactory
            ->createPrimitiveWidgetDefinitionEvaluationContext(
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
        if ($widgetState && !$widgetState instanceof DefinedPrimitiveWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected %s, got %s',
                    DefinedPrimitiveWidgetStateInterface::class,
                    get_class($widgetState)
                )
            );
        }

        return $this->uiEvaluationContextFactory->createPrimitiveWidgetEvaluationContext(
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
        // Create a sub-evaluation context for the primitive widget itself,
        // so that its attributes may be fetched by default expressions for the widget values
        $widgetSubEvaluationContext = $this->createEvaluationContextForWidget(
            $evaluationContext,
            $widget
        );

        $childWidgetStates = [];

        foreach ($widget->getChildWidgets() as $childName => $childWidget) {
            $childWidgetStates[$childName] = $childWidget->createInitialState(
                $childName,
                // Child widgets only get the PrimitiveWidgetEvaluationContext
                // and not the *Definition one, as they do not have access to the attributes
                // and values of the primitive widget
                $widgetSubEvaluationContext,
                $evaluationContextFactory
            );
        }

        $definitionSubEvaluationContext = $this->createDefinitionEvaluationContextForWidget(
            $widgetSubEvaluationContext,
            $widget
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
         * Evaluate the defaults for the values of this widget -
         * defaults can reference attributes of this widget,
         * so that eg. a TextBox widget's "text" value can use a "text" attribute as the default
         */
        $valueStaticBag = $this->valueBagModel->createDefaultStaticBag(
            $definitionSubEvaluationContext
        );

        return $this->uiStateFactory->createDefinedPrimitiveWidgetState(
            $name,
            $widget,
            $attributeStaticBag,
            $valueStaticBag,
            $childWidgetStates
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
     * Evaluates and returns the default expression for a widget value
     *
     * @param string $valueName
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticInterface
     */
    public function getDefaultWidgetValue($valueName, EvaluationContextInterface $evaluationContext)
    {
        return $this->valueBagModel->getDefaultStatic($valueName, $evaluationContext);
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
     * {@inheritdoc}
     */
    public function getWidgetValue(
        $valueName,
        array $widgetStatePath,
        ViewEvaluationContextInterface $evaluationContext
    ) {
        if (!array_key_exists($valueName, $this->valueNameToProviderCallableMap)) {
            throw new LogicException(
                sprintf(
                    'No provider was installed for widget value "%s"',
                    $valueName
                )
            );
        }

        $valueProvider = $this->valueNameToProviderCallableMap[$valueName];
        $valueType = $this->valueBagModel->getStaticType($valueName);

        // Call the provider, passing the unique path to the widget state
        // (the widget state's path could be different to the widget's path,
        // if the widget is inside a repeater, as each repeated instance will get a different state)
        $result = $valueProvider($widgetStatePath);

        // Coerce the result to a static if needed, to allow it to return a non-static
        // and so that any incomplete values are made complete
        return $valueType->coerceNative(
            $result,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $evaluationContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isRenderable()
    {
        return true;
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
        if (!$oldState instanceof DefinedPrimitiveWidgetStateInterface) {
            throw new LogicException(
                sprintf(
                    'Expected %s, got %s',
                    DefinedPrimitiveWidgetStateInterface::class,
                    get_class($oldState)
                )
            );
        }

        // Create a sub-evaluation context for the primitive widget itself,
        // so that its attributes may be fetched by default expressions for the widget values
        $widgetSubEvaluationContext = $this->createEvaluationContextForWidget(
            $evaluationContext,
            $widget,
            $oldState
        );

        $childWidgetStates = [];

        foreach ($widget->getChildWidgets() as $childName => $childWidget) {
            $childWidgetStates[$childName] = $childWidget->reevaluateState(
                $oldState->getChildState($childName),
                // Child widgets only get the PrimitiveWidgetEvaluationContext
                // and not the *Definition one, as they do not have access to the attributes
                // and values of the primitive widget
                $widgetSubEvaluationContext,
                $evaluationContextFactory
            );
        }

        $definitionSubEvaluationContext = $this->createDefinitionEvaluationContextForWidget(
            $widgetSubEvaluationContext,
            $widget,
            $oldState
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
         * Evaluate the values of this widget using their registered providers
         */
        $valueStaticBag = $this->valueBagModel->createBagWithCallback(function (
            $valueName
        ) use (
            $definitionSubEvaluationContext
        ) {
            return $definitionSubEvaluationContext->getWidgetValue($valueName);
        });

        return $oldState->with(
            $attributeStaticBag,
            $valueStaticBag,
            $childWidgetStates
        );
    }
}
