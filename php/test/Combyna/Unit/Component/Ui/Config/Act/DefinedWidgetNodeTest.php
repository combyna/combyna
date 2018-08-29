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
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Trigger\Config\Act\TriggerNode;
use Combyna\Component\Ui\Config\Act\DefinedWidgetNode;
use Combyna\Component\Ui\Config\Act\WidgetNodeInterface;
use Combyna\Component\Validator\Context\ActNodeSubValidationContextInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class DefinedWidgetNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DefinedWidgetNodeTest extends TestCase
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
     * @var DefinedWidgetNode
     */
    private $node;

    /**
     * @var ObjectProphecy|TriggerNode
     */
    private $triggerNode1;

    /**
     * @var ObjectProphecy|TriggerNode
     */
    private $triggerNode2;

    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $visibilityExpressionNode;

    public function setUp()
    {
        $this->attributeExpressionBagNode = $this->prophesize(ExpressionBagNode::class);
        $this->childWidgetNode1 = $this->prophesize(WidgetNodeInterface::class);
        $this->childWidgetNode2 = $this->prophesize(WidgetNodeInterface::class);
        $this->subValidationContext = $this->prophesize(ActNodeSubValidationContextInterface::class);
        $this->triggerNode1 = $this->prophesize(TriggerNode::class);
        $this->triggerNode2 = $this->prophesize(TriggerNode::class);
        $this->visibilityExpressionNode = $this->prophesize(ExpressionNodeInterface::class);

        $this->node = new DefinedWidgetNode(
            'my_lib',
            'my_widget',
            $this->attributeExpressionBagNode->reveal(),
            'my-widget',
            [$this->childWidgetNode1->reveal(), $this->childWidgetNode2->reveal()],
            [$this->triggerNode1->reveal(), $this->triggerNode2->reveal()],
            $this->visibilityExpressionNode->reveal(),
            ['my_app.some_interesting_widget']
        );
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
        $this->assert($this->node->getLibraryName())->exactlyEquals('my_lib');
    }

    public function testGetName()
    {
        $this->assert($this->node->getName())->exactlyEquals('my-widget');
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
        $this->assert($this->node->getWidgetDefinitionName())->exactlyEquals('my_widget');
    }
}
