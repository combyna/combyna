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
use Combyna\Component\Event\EventDefinitionReferenceCollectionInterface;
use Combyna\Component\Event\EventFactoryInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;

/**
 * Class WidgetDefinitionFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionFactory implements WidgetDefinitionFactoryInterface
{
    /**
     * @var EventFactoryInterface
     */
    private $eventFactory;

    /**
     * @var UiStateFactoryInterface
     */
    private $renderedWidgetFactory;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @param UiStateFactoryInterface $renderedWidgetFactory
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     * @param EventFactoryInterface $eventFactory
     */
    public function __construct(
        UiStateFactoryInterface $renderedWidgetFactory,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory,
        EventFactoryInterface $eventFactory
    ) {
        $this->eventFactory = $eventFactory;
        $this->renderedWidgetFactory = $renderedWidgetFactory;
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
        StaticExpressionFactoryInterface $staticExpressionFactory,
        array $valueNameToProviderCallableMap
    ) {
        return new PrimitiveWidgetDefinition(
            $this->renderedWidgetFactory,
            $this->uiEvaluationContextFactory,
            $this->eventFactory,
            $eventDefinitionReferenceCollection,
            $libraryName,
            $name,
            $attributeBagModel,
            $valueBagModel,
            $staticExpressionFactory,
            $valueNameToProviderCallableMap
        );
    }
}
