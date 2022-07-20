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

use Combyna\Component\Expression\ComparisonExpression;
use Combyna\Component\Expression\Config\Act\ComparisonExpressionNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ComparisonExpressionNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ComparisonExpressionNodeTest extends TestCase
{
    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $leftOperandExpressionNode;

    /**
     * @var ComparisonExpressionNode
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
        $this->createExpressionNode(ComparisonExpression::UNEQUAL_CASE_INSENSITIVE);

        static::assertSame($this->leftOperandExpressionNode->reveal(), $this->node->getLeftOperandExpression());
    }

    public function testGetOperatorFetchesTheOperator()
    {
        $this->createExpressionNode(ComparisonExpression::UNEQUAL_CASE_INSENSITIVE);

        static::assertSame(ComparisonExpression::UNEQUAL_CASE_INSENSITIVE, $this->node->getOperator());
    }

    public function testGetRightOperandExpressionFetchesTheExpression()
    {
        $this->createExpressionNode(ComparisonExpression::UNEQUAL_CASE_INSENSITIVE);

        static::assertSame($this->rightOperandExpressionNode->reveal(), $this->node->getRightOperandExpression());
    }

    public function testGetType()
    {
        $this->createExpressionNode(ComparisonExpression::EQUAL_CASE_INSENSITIVE);

        static::assertSame('comparison', $this->node->getType());
    }

    /**
     * @param string $operator
     */
    private function createExpressionNode($operator)
    {
        $this->node = new ComparisonExpressionNode(
            $this->leftOperandExpressionNode->reveal(),
            $operator,
            $this->rightOperandExpressionNode->reveal()
        );
    }
}
