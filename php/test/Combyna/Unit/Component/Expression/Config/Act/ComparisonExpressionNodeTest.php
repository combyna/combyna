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

        $this->assert($this->node->getLeftOperandExpression())->isTheSameAs($this->leftOperandExpressionNode->reveal());
    }

    public function testGetOperatorFetchesTheOperator()
    {
        $this->createExpressionNode(ComparisonExpression::UNEQUAL_CASE_INSENSITIVE);

        $this->assert($this->node->getOperator())->isTheSameAs(ComparisonExpression::UNEQUAL_CASE_INSENSITIVE);
    }

    public function testGetRightOperandExpressionFetchesTheExpression()
    {
        $this->createExpressionNode(ComparisonExpression::UNEQUAL_CASE_INSENSITIVE);

        $this->assert($this->node->getRightOperandExpression())->isTheSameAs($this->rightOperandExpressionNode->reveal());
    }

    public function testGetType()
    {
        $this->createExpressionNode(ComparisonExpression::EQUAL_CASE_INSENSITIVE);

        $this->assert($this->node->getType())->exactlyEquals('comparison');
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
