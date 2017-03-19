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

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use LogicException;

/**
 * Class TextWidget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextWidget implements WidgetInterface
{
    const DEFINITION = 'text';

    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var int
     */
    private $id;

    /**
     * @var WidgetInterface
     */
    private $parentWidget;

    /**
     * @var RenderedWidgetFactory
     */
    private $renderedWidgetFactory;

    /**
     * @var ExpressionInterface
     */
    private $textExpression;

    /**
     * @var ExpressionInterface|null
     */
    private $visibilityExpression;

    /**
     * @param WidgetInterface $parentWidget
     * @param int $id
     * @param ExpressionInterface $textExpression
     * @param BagFactoryInterface $bagFactory
     * @param RenderedWidgetFactory $renderedWidgetFactory
     * @param ExpressionInterface|null $visibilityExpression
     */
    public function __construct(
        WidgetInterface $parentWidget,
        $id,
        ExpressionInterface $textExpression,
        BagFactoryInterface $bagFactory,
        RenderedWidgetFactory $renderedWidgetFactory,
        ExpressionInterface $visibilityExpression = null
    ) {
        $this->bagFactory = $bagFactory;
        $this->id = $id;
        $this->parentWidget = $parentWidget;
        $this->renderedWidgetFactory = $renderedWidgetFactory;
        $this->textExpression = $textExpression;
        $this->visibilityExpression = $visibilityExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild($childName, WidgetInterface $childWidget)
    {
        throw new LogicException('TextWidget at path "' . $this->getPath() . '" does not support children');
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidAttributeStaticBag(StaticBagInterface $attributeStaticBag)
    {
        $attributeBagModel = $this->bagFactory->createFixedStaticBagModel([
            $this->bagFactory->createFixedStaticDefinition(
                'text',
                new StaticType(TextExpression::class)
            )
        ]);

        $attributeBagModel->assertValidStaticBag($attributeStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionLibraryName()
    {
        return self::LIBRARY;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionName()
    {
        return self::DEFINITION;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->parentWidget->getPath() . '-' . $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function render(ViewEvaluationContextInterface $evaluationContext)
    {
        $textStatic = $this->textExpression->toStatic($evaluationContext);

        return $this->renderedWidgetFactory->createRenderedWidget(
            $this,
            $this->bagFactory->createStaticBag([
                'text' => $textStatic
            ])
        );
    }
}
