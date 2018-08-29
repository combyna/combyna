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

        $this->assert($this->node->getLeftOperandExpression())->isTheSameAs($this->leftOperandExpressionNode->reveal());
    }

    public function testGetOperatorFetchesTheOperator()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::MULTIPLY);

        $this->assert($this->node->getOperator())->exactlyEquals(BinaryArithmeticExpression::MULTIPLY);
    }

    public function testGetRightOperandExpressionFetchesTheExpression()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::ADD);

        $this->assert($this->node->getRightOperandExpression())->isTheSameAs($this->rightOperandExpressionNode->reveal());
    }

    public function testGetType()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::ADD);

        $this->assert($this->node->getType())->exactlyEquals('binary-arithmetic');
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
