<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Ui\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Trigger\Config\Act\TriggerNode;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionNodeInterface;
use Combyna\Component\Ui\Config\Act\WidgetNode;
use Combyna\Component\Ui\Config\Act\WidgetNodeInterface;
use Combyna\Component\Validator\Context\ActNodeValidationContextInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class WidgetNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetNodeTest extends TestCase
{
    /**
     * @var ObjectProphecy|ExpressionBagNode
     */
    private $attributeExpressionBagNode;

    /**
     * @var ObjectProphecy|WidgetNodeInterface
     */
    private $childWidgetNode1;

    /**
     * @var ObjectProphecy|WidgetNodeInterface
     */
    private $childWidgetNode2;

    /**
     * @var WidgetNode
     */
    private $node;

    /**
     * @var ObjectProphecy|ActNodeValidationContextInterface
     */
    private $subValidationContext;

    /**
     * @var ObjectProphecy|TriggerNode
     */
    private $triggerNode1;

    /**
     * @var ObjectProphecy|TriggerNode
     */
    private $triggerNode2;

    /**
     * @var ObjectProphecy|ActNodeValidationContextInterface
     */
    private $validationContext;

    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $visibilityExpressionNode;

    /**
     * @var ObjectProphecy|WidgetDefinitionNodeInterface
     */
    private $widgetDefinitionNode;

    public function setUp()
    {
        $this->attributeExpressionBagNode = $this->prophesize(ExpressionBagNode::class);
        $this->childWidgetNode1 = $this->prophesize(WidgetNodeInterface::class);
        $this->childWidgetNode2 = $this->prophesize(WidgetNodeInterface::class);
        $this->subValidationContext = $this->prophesize(ActNodeValidationContextInterface::class);
        $this->triggerNode1 = $this->prophesize(TriggerNode::class);
        $this->triggerNode2 = $this->prophesize(TriggerNode::class);
        $this->validationContext = $this->prophesize(ActNodeValidationContextInterface::class);
        $this->visibilityExpressionNode = $this->prophesize(ExpressionNodeInterface::class);
        $this->widgetDefinitionNode = $this->prophesize(WidgetDefinitionNodeInterface::class);

        $this->node = new WidgetNode(
            $this->widgetDefinitionNode->reveal(),
            $this->attributeExpressionBagNode->reveal(),
            [$this->childWidgetNode1->reveal(), $this->childWidgetNode2->reveal()],
            [$this->triggerNode1->reveal(), $this->triggerNode2->reveal()],
            $this->visibilityExpressionNode->reveal(),
            ['my_app.some_interesting_widget']
        );

        $this->validationContext->createSubActNodeContext(Argument::exact($this->node))
            ->willReturn($this->subValidationContext->reveal());
    }

    public function testGetAttributeExpressionBag()
    {
        $this->assert($this->node->getAttributeExpressionBag())
            ->exactlyEquals($this->attributeExpressionBagNode->reveal());
    }

    public function testGetChildWidgets()
    {
        $this->assert($this->node->getChildWidgets())
            ->exactlyEquals([$this->childWidgetNode1->reveal(), $this->childWidgetNode2->reveal()]);
    }

    public function testGetLibraryName()
    {
        $this->widgetDefinitionNode->getLibraryName()->willReturn('my_lib');

        $this->assert($this->node->getLibraryName())->exactlyEquals('my_lib');
    }

    public function testGetTags()
    {
        $this->assert($this->node->getTags())->exactlyEquals(['my_app.some_interesting_widget']);
    }

    public function testGetTriggers()
    {
        $this->assert($this->node->getTriggers())
            ->exactlyEquals([$this->triggerNode1->reveal(), $this->triggerNode2->reveal()]);
    }

    public function testGetVisibilityExpression()
    {
        $this->assert($this->node->getVisibilityExpression())
            ->exactlyEquals($this->visibilityExpressionNode->reveal());
    }

    public function testGetWidgetDefinitionName()
    {
        $this->widgetDefinitionNode->getWidgetDefinitionName()->willReturn('my_widget');

        $this->assert($this->node->getWidgetDefinitionName())->exactlyEquals('my_widget');
    }

    public function testValidateValidatesAttributeExpressionBagNodeWithASubContext()
    {
        $this->node->validate($this->validationContext->reveal());

        $this->attributeExpressionBagNode
            ->validate(Argument::exact($this->subValidationContext->reveal()))
            ->shouldHaveBeenCalled();
    }

    public function testValidateValidatesVisibilityExpressionNodeWithASubContext()
    {
        $this->node->validate($this->validationContext->reveal());

        $this->visibilityExpressionNode
            ->validate(Argument::exact($this->subValidationContext->reveal()))
            ->shouldHaveBeenCalled();
    }

    public function testValidateValidatesThatVisibilityExpressionWillEvaluateToABoolean()
    {
        $this->node->validate($this->validationContext->reveal());

        $this->subValidationContext
            ->assertResultType(
                Argument::exact($this->visibilityExpressionNode->reveal()),
                new StaticType(BooleanExpression::class),
                Argument::any()
            )
            ->shouldHaveBeenCalled();
    }

    public function testValidateValidatesWidgetAgainstTheDefinitionWithASubContext()
    {
        $this->node->validate($this->validationContext->reveal());

        $this->widgetDefinitionNode
            ->validateWidget(
                Argument::exact($this->subValidationContext->reveal()),
                Argument::exact($this->attributeExpressionBagNode->reveal()),
                Argument::exact([
                    $this->childWidgetNode1->reveal(),
                    $this->childWidgetNode2->reveal()
                ])
            )
            ->shouldHaveBeenCalled();
    }

    public function testValidateValidatesAllTriggerNodesWithASubContext()
    {
        $this->node->validate($this->validationContext->reveal());

        $this->triggerNode1
            ->validate(Argument::exact($this->subValidationContext->reveal()))
            ->shouldHaveBeenCalled();
        $this->triggerNode2
            ->validate(Argument::exact($this->subValidationContext->reveal()))
            ->shouldHaveBeenCalled();
    }

    public function testValidateValidatesAllChildWidgetNodesWithASubContext()
    {
        $this->node->validate($this->validationContext->reveal());

        $this->childWidgetNode1
            ->validate(Argument::exact($this->subValidationContext->reveal()))
            ->shouldHaveBeenCalled();
        $this->childWidgetNode2
            ->validate(Argument::exact($this->subValidationContext->reveal()))
            ->shouldHaveBeenCalled();
    }
}
