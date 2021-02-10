<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\View;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Class OverlayView
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class OverlayView implements OverlayViewInterface
{
    /**
     * @var FixedStaticBagModelInterface
     */
    private $attributeBagModel;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $name;

    /**
     * @var UiStateFactoryInterface
     */
    private $renderedWidgetFactory;

    /**
     * @var WidgetInterface
     */
    private $rootWidget;

    /**
     * @var ExpressionInterface
     */
    private $titleExpression;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @var ExpressionInterface|null
     */
    private $visibilityExpression;

    /**
     * @param string $name
     * @param ExpressionInterface $titleExpression
     * @param string $description
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @param WidgetInterface $rootWidget
     * @param UiStateFactoryInterface $renderedWidgetFactory
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     * @param ExpressionInterface|null $visibilityExpression
     */
    public function __construct(
        $name,
        ExpressionInterface $titleExpression,
        $description,
        FixedStaticBagModelInterface $attributeBagModel,
        WidgetInterface $rootWidget,
        UiStateFactoryInterface $renderedWidgetFactory,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory,
        ExpressionInterface $visibilityExpression = null
    ) {
        $this->attributeBagModel = $attributeBagModel;
        $this->description = $description;
        $this->name = $name;
        $this->renderedWidgetFactory = $renderedWidgetFactory;
        $this->rootWidget = $rootWidget;
        $this->titleExpression = $titleExpression;
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
        $this->visibilityExpression = $visibilityExpression;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
