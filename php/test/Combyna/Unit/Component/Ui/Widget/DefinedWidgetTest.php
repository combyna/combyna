<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Ui\Widget;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Trigger\TriggerCollectionInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\Widget\DefinedWidget;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class DefinedWidgetTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DefinedWidgetTest extends TestCase
{
    /**
     * @var ObjectProphecy|ExpressionBagInterface
     */
    private $attributeExpressionBag;

    /**
     * @var ObjectProphecy|ExpressionBagInterface
     */
    private $captureExpressionBag;

    /**
     * @var ObjectProphecy|FixedStaticBagModelInterface
     */
    private $captureStaticBagModel;

    /**
     * @var ObjectProphecy|WidgetDefinitionInterface
     */
    private $definition;

    /**
     * @var ObjectProphecy|WidgetInterface
     */
    private $parentWidget;

    /**
     * @var ObjectProphecy|TriggerCollectionInterface
     */
    private $triggerCollection;

    /**
     * @var ObjectProphecy|UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @var ObjectProphecy|UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @var DefinedWidget
     */
    private $widget;

    public function setUp()
    {
        $this->attributeExpressionBag = $this->prophesize(ExpressionBagInterface::class);
        $this->captureExpressionBag = $this->prophesize(ExpressionBagInterface::class);
        $this->captureStaticBagModel = $this->prophesize(FixedStaticBagModelInterface::class);
        $this->definition = $this->prophesize(WidgetDefinitionInterface::class);
        $this->parentWidget = $this->prophesize(WidgetInterface::class);
        $this->triggerCollection = $this->prophesize(TriggerCollectionInterface::class);
        $this->uiEvaluationContextFactory = $this->prophesize(UiEvaluationContextFactoryInterface::class);
        $this->uiStateFactory = $this->prophesize(UiStateFactoryInterface::class);

        $this->widget = new DefinedWidget(
            $this->parentWidget->reveal(),
            'my_widget',
            $this->definition->reveal(),
            $this->attributeExpressionBag->reveal(),
            $this->uiStateFactory->reveal(),
            $this->uiEvaluationContextFactory->reveal(),
            $this->triggerCollection->reveal(),
            $this->captureStaticBagModel->reveal(),
            $this->captureExpressionBag->reveal(),
            null, // TODO: Get rid of visibility expressions
            ['first_tag', 'second_tag']
        );
    }

    public function testGetTriggersReturnsTheTriggerCollection()
    {
        static::assertSame($this->triggerCollection->reveal(), $this->widget->getTriggers());
    }
}
