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
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Trigger\Config\Act\TriggerNode;
use Combyna\Component\Ui\Config\Act\DefinedWidgetNode;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionReferenceNode;
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
     * @var ObjectProphecy|ExpressionBagNode
     */
    private $captureExpressionBagNode;

    /**
     * @var ObjectProphecy|FixedStaticBagModelNodeInterface
     */
    private $captureStaticBagModelNode;

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
        $this->captureExpressionBagNode = $this->prophesize(ExpressionBagNode::class);
        $this->captureStaticBagModelNode = $this->prophesize(FixedStaticBagModelNodeInterface::class);
        $this->childWidgetNode1 = $this->prophesize(WidgetNodeInterface::class);
        $this->childWidgetNode2 = $this->prophesize(WidgetNodeInterface::class);
        $this->subValidationContext = $this->prophesize(ActNodeSubValidationContextInterface::class);
        $this->triggerNode1 = $this->prophesize(TriggerNode::class);
        $this->triggerNode2 = $this->prophesize(TriggerNode::class);
        $this->visibilityExpressionNode = $this->prophesize(ExpressionNodeInterface::class);

        $this->node = new DefinedWidgetNode(
            new WidgetDefinitionReferenceNode('my_lib', 'my_widget'),
            $this->attributeExpressionBagNode->reveal(),
            $this->captureStaticBagModelNode->reveal(),
            $this->captureExpressionBagNode->reveal(),
            'my-widget',
            [$this->childWidgetNode1->reveal(), $this->childWidgetNode2->reveal()],
            [$this->triggerNode1->reveal(), $this->triggerNode2->reveal()],
            $this->visibilityExpressionNode->reveal(),
            ['my_app.some_interesting_widget']
        );
    }

    public function testGetAttributeExpressionBag()
    {
        static::assertSame(
            $this->attributeExpressionBagNode->reveal(),
            $this->node->getAttributeExpressionBag()
        );
    }

    public function testGetChildWidgets()
    {
        static::assertSame(
            [$this->childWidgetNode1->reveal(), $this->childWidgetNode2->reveal()],
            $this->node->getChildWidgets()
        );
    }

    public function testGetLibraryName()
    {
        static::assertSame('my_lib', $this->node->getLibraryName());
    }

    public function testGetName()
    {
        static::assertSame('my-widget', $this->node->getName());
    }

    public function testGetTags()
    {
        static::assertSame(['my_app.some_interesting_widget'], $this->node->getTags());
    }

    public function testGetTriggers()
    {
        static::assertSame(
            [$this->triggerNode1->reveal(), $this->triggerNode2->reveal()],
            $this->node->getTriggers()
        );
    }

    public function testGetVisibilityExpression()
    {
        static::assertSame(
            $this->visibilityExpressionNode->reveal(),
            $this->node->getVisibilityExpression()
        );
    }

    public function testGetWidgetDefinitionName()
    {
        static::assertSame('my_widget', $this->node->getWidgetDefinitionName());
    }
}
