<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;

/**
 * Class ViewFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewFactory implements ViewFactoryInterface
{
    /**
     * @var int
     */
    private $nextWidgetId = 0;

    /**
     * @var RenderedWidgetFactoryInterface
     */
    private $renderedWidgetFactory;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @param RenderedWidgetFactoryInterface $renderedWidgetFactory
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     */
    public function __construct(
        RenderedWidgetFactoryInterface $renderedWidgetFactory,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
    ) {
        $this->renderedWidgetFactory = $renderedWidgetFactory;
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createView(
        $name,
        ExpressionInterface $titleExpression,
        $description,
        FixedStaticBagModelInterface $attributeBagModel,
        WidgetInterface $rootWidget,
        ExpressionInterface $visibilityExpression = null
    ) {
        return new View(
            $name,
            $titleExpression,
            $description,
            $attributeBagModel,
            $rootWidget,
            $this->renderedWidgetFactory,
            $this->uiEvaluationContextFactory,
            $visibilityExpression
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createViewCollection(array $views)
    {
        return new ViewCollection($views);
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(
        WidgetDefinitionInterface $widgetDefinition,
        ExpressionBagInterface $attributeExpressionBag,
        WidgetInterface $parentWidget = null,
        ExpressionInterface $visibilityExpression = null
    ) {
        return new Widget(
            $parentWidget,
            $this->nextWidgetId++,
            $widgetDefinition,
            $attributeExpressionBag,
            $this->renderedWidgetFactory,
            $visibilityExpression
        );
    }
}
