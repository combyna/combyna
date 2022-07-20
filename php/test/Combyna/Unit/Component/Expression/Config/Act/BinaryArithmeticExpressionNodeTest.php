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

use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Expression\Config\Act\BinaryArithmeticExpressionNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class BinaryArithmeticExpressionNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BinaryArithmeticExpressionNodeTest extends TestCase
{
    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $leftOperandExpressionNode;

    /**
     * @var BinaryArithmeticExpressionNode
     */
    private $node;

    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $rightOperandExpressionNode;

    public function setUp()
    {
        $this->leftOperandExpressionNode = $this->prophesize(ExpressionNodeInterface::class);
        $this->rightOperandExpressionNode = $this->prophesize(ExpressionNodeInterface::class);
    }

    public function testGetLeftOperandExpressionFetchesTheExpression()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::ADD);

        static::assertSame($this->leftOperandExpressionNode->reveal(), $this->node->getLeftOperandExpression());
    }

    public function testGetOperatorFetchesTheOperator()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::MULTIPLY);

        static::assertSame(BinaryArithmeticExpression::MULTIPLY, $this->node->getOperator());
    }

    public function testGetRightOperandExpressionFetchesTheExpression()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::ADD);

        static::assertSame($this->rightOperandExpressionNode->reveal(), $this->node->getRightOperandExpression());
    }

    public function testGetType()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::ADD);

        static::assertSame('binary-arithmetic', $this->node->getType());
    }

    /**
     * @param string $operator
     */
    private function createExpressionNode($operator)
    {
        $this->node = new BinaryArithmeticExpressionNode(
            $this->leftOperandExpressionNode->reveal(),
            $operator,
            $this->rightOperandExpressionNode->reveal()
        );
    }
}
