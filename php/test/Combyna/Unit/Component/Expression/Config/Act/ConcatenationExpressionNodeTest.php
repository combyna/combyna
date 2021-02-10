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
        $this->assert($this->node->getGlueExpression())->isTheSameAs($this->glueExpressionNode->reveal());
    }

    public function testGetGlueExpressionReturnsNullWhenNotSet()
    {
        $node = new ConcatenationExpressionNode($this->operandListExpressionNode->reveal());

        $this->assert($node->getGlueExpression())->isNull;
    }

    public function testGetOperandListExpressionFetchesTheExpressionWhenSet()
    {
        $this->assert($this->node->getOperandListExpression())->isTheSameAs($this->operandListExpressionNode->reveal());
    }

    public function testGetType()
    {
        $this->assert($this->node->getType())->exactlyEquals('concatenation');
    }
}
