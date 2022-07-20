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
 * Class EmbedView
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EmbedView implements EmbedViewInterface
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
     * @var UiStateFactoryInterface
     */
    private $widgetStateFactory;

    /**
     * @param string $name
     * @param ExpressionInterface $titleExpression
     * @param string $description
     * @param FixedStaticBagModelInterface $attributeBagModel
     * @param WidgetInterface $rootWidget
     * @param UiStateFactoryInterface $widgetStateFactory
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     */
    public function __construct(
        $name,
        ExpressionInterface $titleExpression,
        $description,
        FixedStaticBagModelInterface $attributeBagModel,
        WidgetInterface $rootWidget,
        UiStateFactoryInterface $widgetStateFactory,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
    ) {
        $this->attributeBagModel = $attributeBagModel;
        $this->description = $description;
        $this->name = $name;
        $this->rootWidget = $rootWidget;
        $this->titleExpression = $titleExpression;
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
        $this->widgetStateFactory = $widgetStateFactory;
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
    public function createInitialState(StaticBagInterface $viewAttributeStaticBag)
    {
        throw new \Exception('Not implemented');
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
