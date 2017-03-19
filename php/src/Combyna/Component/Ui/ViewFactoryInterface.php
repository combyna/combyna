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

/**
 * Interface ViewFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewFactoryInterface
{
    /**
     * Creates a new view
     *
     * @param string $name
     * @param ExpressionInterface $titleExpression
     * @param $description
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @param WidgetInterface $rootWidget
     * @param ExpressionInterface|null $visibilityExpression
     * @return ViewInterface
     */
    public function createView(
        $name,
        ExpressionInterface $titleExpression,
        $description,
        FixedStaticBagModelInterface $attributeBagModel,
        WidgetInterface $rootWidget,
        ExpressionInterface $visibilityExpression = null
    );

    /**
     * Creates a collection of views
     *
     * @param ViewInterface[] $views
     * @return ViewCollectionInterface
     */
    public function createViewCollection(array $views);

    /**
     * Creates a new widget
     *
     * @param WidgetDefinitionInterface $widgetDefinition
     * @param ExpressionBagInterface $attributeExpressionBag
     * @param WidgetInterface|null $parentWidget
     * @param ExpressionInterface|null $visibilityExpression
     * @return WidgetInterface
     */
    public function createWidget(
        WidgetDefinitionInterface $widgetDefinition,
        ExpressionBagInterface $attributeExpressionBag,
        WidgetInterface $parentWidget = null,
        ExpressionInterface $visibilityExpression = null
    );
}
