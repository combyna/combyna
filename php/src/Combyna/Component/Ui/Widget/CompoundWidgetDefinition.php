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
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\Event\Exception\EventDefinitionNotReferencedByWidgetException;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;

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
    public function createEvaluationContextForWidget(
        ViewEvaluationContextInterface $parentContext,
        DefinedWidgetInterface $widget,
        DefinedWidgetStateInterface $widgetState
    ) {
        return $this->uiEvaluationContextFactory->createCompoundWidgetEvaluationContext(
            $parentContext,
            $widget,
            $widgetState->getAttributeStaticBag(),
            $this->valueExpressionBag
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createEvent($libraryName, $eventName, StaticBagInterface $payloadStaticBag)
    {
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

        return $this->eventFactory->createEvent($eventDefinition, $payloadStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialStateForWidget(
        $name,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        array $childWidgetStates,
        ViewEvaluationContextInterface $evaluationContext
    ) {
        // Create a sub-evaluation context for the compound widget itself,
        // so that its attributes may be fetched by expressions inside the root widget's structure
        $compoundWidgetSubEvaluationContext = $this->uiEvaluationContextFactory->createCompoundWidgetEvaluationContext(
            $evaluationContext,
            $widget,
            $attributeStaticBag,
            $this->valueExpressionBag
        );
        $rootWidgetState = $this->rootWidget->createInitialState(
            'root',
            $compoundWidgetSubEvaluationContext
        );

        /*
         * Evaluate the expressions for the values of this widget -
         * values can reference attributes of this widget
         */
        $valueStaticBag = $this->valueExpressionBag->toStaticBag(
            $compoundWidgetSubEvaluationContext
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
    public function isRenderable()
    {
        return false;
    }
}
