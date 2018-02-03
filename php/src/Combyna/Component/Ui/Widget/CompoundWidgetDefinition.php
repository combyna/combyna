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

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Event\EventDefinitionReferenceCollectionInterface;
use Combyna\Component\Event\EventFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;

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
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @param UiStateFactoryInterface $uiStateFactory
     * @param EventFactoryInterface $eventFactory
     * @param EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection
     * @param string $libraryName
     * @param string $name
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @param WidgetInterface $rootWidget
     */
    public function __construct(
        UiStateFactoryInterface $uiStateFactory,
        EventFactoryInterface $eventFactory,
        EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection,
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel,
        WidgetInterface $rootWidget
    ) {
        $this->attributeBagModel = $attributeBagModel;
        $this->eventDefinitionReferenceCollection = $eventDefinitionReferenceCollection;
        $this->eventFactory = $eventFactory;
        $this->libraryName = $libraryName;
        $this->name = $name;
        $this->rootWidget = $rootWidget;
        $this->uiStateFactory = $uiStateFactory;
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
    public function createEvent($libraryName, $eventName, StaticBagInterface $payloadStaticBag)
    {
        $eventDefinition = $this->eventDefinitionReferenceCollection->getDefinitionByName($libraryName, $eventName);

        return $this->eventFactory->createEvent($eventDefinition, $payloadStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createInitialState(
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        array $childWidgetStates,
        ViewEvaluationContextInterface $evaluationContext
    ) {
        // Create a sub-evaluation context for the compound widget itself,
        // so that its attributes may be fetched by expressions inside the root widget's structure
        $compoundDefinitionRootWidgetSubEvaluationContext = $evaluationContext->createSubWidgetEvaluationContext(
            $widget
        );
        $rootWidgetState = $this->rootWidget->createInitialState($compoundDefinitionRootWidgetSubEvaluationContext);

        return $this->uiStateFactory->createDefinedCompoundWidgetState(
            $widget,
            $attributeStaticBag,
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
