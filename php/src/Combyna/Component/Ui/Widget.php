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
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;

/**
 * Class Widget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Widget implements WidgetInterface
{
    /**
     * @var ExpressionBagInterface
     */
    private $attributeExpressions;

    /**
     * @var WidgetInterface[]
     */
    private $childWidgets = [];

    /**
     * @var WidgetDefinitionInterface
     */
    private $definition;

    /**
     * @var int
     */
    private $id;

    /**
     * @var WidgetInterface|null
     */
    private $parentWidget;

    /**
     * @var RenderedWidgetFactoryInterface
     */
    private $renderedWidgetFactory;

    /**
     * @var ExpressionInterface|null
     */
    private $visibilityExpression;

    /**
     * @param WidgetInterface|null $parentWidget
     * @param int $id
     * @param WidgetDefinitionInterface $definition
     * @param ExpressionBagInterface $attributeExpressions
     * @param RenderedWidgetFactoryInterface $renderedWidgetFactory
     * @param ExpressionInterface|null $visibilityExpression
     */
    public function __construct(
        WidgetInterface $parentWidget = null,
        $id,
        WidgetDefinitionInterface $definition,
        ExpressionBagInterface $attributeExpressions,
        RenderedWidgetFactoryInterface $renderedWidgetFactory,
        ExpressionInterface $visibilityExpression = null
    ) {
        $this->attributeExpressions = $attributeExpressions;
        $this->childWidgets = [];
        $this->definition = $definition;
        $this->id = $id;
        $this->parentWidget = $parentWidget;
        $this->renderedWidgetFactory = $renderedWidgetFactory;
        $this->visibilityExpression = $visibilityExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild($childName, WidgetInterface $childWidget)
    {
        $this->childWidgets[$childName] = $childWidget;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidAttributeStaticBag(StaticBagInterface $attributeStaticBag)
    {
        $this->definition->assertValidAttributeStaticBag($attributeStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionLibraryName()
    {
        return $this->definition->getLibraryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionName()
    {
        return $this->definition->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        $prefix = ($this->parentWidget !== null) ?
            $this->parentWidget->getPath() . '-' :
            '';

        return $prefix . $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function render(ViewEvaluationContextInterface $evaluationContext)
    {
        if ($this->visibilityExpression) {
            $visibleStatic = $this->visibilityExpression->toStatic($evaluationContext);

            if ($visibleStatic->toNative() === false) {
                // Widget is invisible
                return null;
            }
        }

        $attributeStaticBag = $this->attributeExpressions->toStaticBag($evaluationContext);

        $widgetEvaluationContext = $evaluationContext->createSubWidgetEvaluationContext($this);
        $renderedChildWidgets = [];

        foreach ($this->childWidgets as $childWidget) {
            $renderedChildWidget = $childWidget->render($widgetEvaluationContext);

            if ($renderedChildWidget) {
                $renderedChildWidgets[] = $renderedChildWidget;
            }
        }

        return $this->renderedWidgetFactory->createRenderedWidget(
            $this,
            $attributeStaticBag,
            $renderedChildWidgets
        );
    }
}
