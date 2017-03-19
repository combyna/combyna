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

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;

/**
 * Class View
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class View implements ViewInterface
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
     * @var RenderedWidgetFactory
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
     * @param RenderedWidgetFactory $renderedWidgetFactory
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     * @param ExpressionInterface|null $visibilityExpression
     */
    public function __construct(
        $name,
        ExpressionInterface $titleExpression,
        $description,
        FixedStaticBagModelInterface $attributeBagModel,
        WidgetInterface $rootWidget,
        RenderedWidgetFactory $renderedWidgetFactory,
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

    /**
     * {@inheritdoc}
     */
    public function render(
        StaticBagInterface $viewAttributeStaticBag,
        EvaluationContextInterface $rootEvaluationContext
    ) {
        $viewEvaluationContext = $this->uiEvaluationContextFactory->createViewEvaluationContext(
            $rootEvaluationContext,
            $viewAttributeStaticBag
        );

        if ($this->visibilityExpression) {
            $visibleStatic = $this->visibilityExpression->toStatic($viewEvaluationContext);

            if ($visibleStatic->toNative() === false) {
                // View is invisible
                return null;
            }
        }

        $renderedRootWidget = $this->rootWidget->render($viewEvaluationContext);

        return $this->renderedWidgetFactory->createRenderedView(
            $this,
            $viewAttributeStaticBag,
            $renderedRootWidget
        );
    }
}
