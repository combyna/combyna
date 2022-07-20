<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Expression;

use Combyna\Component\Expression\Config\Act\ConcatenationExpressionNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ConcatenationExpressionNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConcatenationExpressionNodeTest extends TestCase
{
    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $glueExpressionNode;

    /**
     * @var ConcatenationExpressionNode
     */
    private $node;

    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $operandListExpressionNode;

    public function setUp()
    {
        $this->glueExpressionNode = $this->prophesize(ExpressionNodeInterface::class);
        $this->operandListExpressionNode = $this->prophesize(ExpressionNodeInterface::class);

        $this->node = new ConcatenationExpressionNode(
            $this->operandListExpressionNode->reveal(),
            $this->glueExpressionNode->reveal()
        );
    }

    public function testGetGlueExpressionFetchesTheExpressionWhenSet()
    {
        static::assertSame($this->glueExpressionNode->reveal(), $this->node->getGlueExpression());
    }

    public function testGetGlueExpressionReturnsNullWhenNotSet()
    {
        $node = new ConcatenationExpressionNode($this->operandListExpressionNode->reveal());

        static::assertNull($node->getGlueExpression());
    }

    public function testGetOperandListExpressionFetchesTheExpressionWhenSet()
    {
        static::assertSame($this->operandListExpressionNode->reveal(), $this->node->getOperandListExpression());
    }

    public function testGetType()
    {
        static::assertSame('concatenation', $this->node->getType());
    }
}
