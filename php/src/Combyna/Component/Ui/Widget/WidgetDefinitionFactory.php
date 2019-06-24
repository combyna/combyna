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
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Event\EventDefinitionReferenceCollectionInterface;
use Combyna\Component\Event\EventFactoryInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;

/**
 * Class WidgetDefinitionFactory
 *
 * TODO: Rename to just WidgetFactory to match SignalFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionFactory implements WidgetDefinitionFactoryInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var EventFactoryInterface
     */
    private $eventFactory;

    /**
     * @var UiStateFactoryInterface
     */
    private $renderedWidgetFactory;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @param BagFactoryInterface $bagFactory
     * @param UiStateFactoryInterface $renderedWidgetFactory
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     * @param EventFactoryInterface $eventFactory
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     */
    public function __construct(
        BagFactoryInterface $bagFactory,
        UiStateFactoryInterface $renderedWidgetFactory,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory,
        EventFactoryInterface $eventFactory,
        StaticExpressionFactoryInterface $staticExpressionFactory
    ) {
        $this->bagFactory = $bagFactory;
        $this->eventFactory = $eventFactory;
        $this->renderedWidgetFactory = $renderedWidgetFactory;
        $this->staticExpressionFactory = $staticExpressionFactory;
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createCompoundWidgetDefinition(
        EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection,
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel,
        ExpressionBagInterface $valueExpressionBag,
        WidgetInterface $rootWidget
    ) {
        return new CompoundWidgetDefinition(
            $this->renderedWidgetFactory,
            $this->uiEvaluationContextFactory,
            $this->eventFactory,
            $eventDefinitionReferenceCollection,
            $libraryName,
            $name,
            $attributeBagModel,
            $valueExpressionBag,
            $rootWidget
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createPrimitiveWidgetDefinition(
        EventDefinitionReferenceCollectionInterface $eventDefinitionReferenceCollection,
        $libraryName,
        $name,
        FixedStaticBagModelInterface $attributeBagModel,
        FixedStaticBagModelInterface $valueBagModel,
        array $valueNameToProviderCallableMap
    ) {
        return new PrimitiveWidgetDefinition(
            $this->bagFactory,
            $this->renderedWidgetFactory,
            $this->uiEvaluationContextFactory,
            $this->eventFactory,
            $eventDefinitionReferenceCollection,
            $libraryName,
            $name,
            $attributeBagModel,
            $valueBagModel,
            $this->staticExpressionFactory,
            $valueNameToProviderCallableMap
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetDefinitionCollection(array $widgetDefinitions, $libraryName)
    {
        return new WidgetDefinitionCollection($widgetDefinitions, $libraryName);
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetDefinitionRepository(
        EnvironmentInterface $environment,
        WidgetDefinitionCollectionInterface $appWidgetDefinitionCollection
    ) {
        return new WidgetDefinitionRepository($environment, $appWidgetDefinitionCollection);
    }
}
