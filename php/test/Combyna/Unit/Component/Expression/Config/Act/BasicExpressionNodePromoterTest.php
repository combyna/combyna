<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Expression\Config\Act;

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\ExpressionBag;
use Combyna\Component\Expression\Config\Act\BasicExpressionNodePromoter;
use Combyna\Component\Expression\Config\Act\DelegatingExpressionNodePromoter;
use Combyna\Component\Expression\Config\Act\FunctionExpressionNode;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\FunctionExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Framework\Context\ModeContext;
use Combyna\Component\Framework\Mode\DevelopmentMode;
use Combyna\Component\Framework\Mode\ProductionMode;
use Combyna\Component\Type\AnyType;
use Combyna\Component\Type\StaticType;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class BasicExpressionNodePromoterTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BasicExpressionNodePromoterTest extends TestCase
{
    /**
     * @var ObjectProphecy|BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var ObjectProphecy|DelegatingExpressionNodePromoter
     */
    private $delegatingExpressionNodePromoter;

    /**
     * @var ObjectProphecy|ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ObjectProphecy|ModeContext
     */
    private $modeContext;

    /**
     * @var BasicExpressionNodePromoter
     */
    private $promoter;

    public function setUp()
    {
        $this->bagNodePromoter = $this->prophesize(BagNodePromoter::class);
        $this->delegatingExpressionNodePromoter = $this->prophesize(DelegatingExpressionNodePromoter::class);
        $this->expressionFactory = $this->prophesize(ExpressionFactoryInterface::class);
        $this->modeContext = $this->prophesize(ModeContext::class);
        $this->modeContext->getMode()->willReturn(new DevelopmentMode());

        $this->promoter = new BasicExpressionNodePromoter(
            $this->expressionFactory->reveal(),
            $this->delegatingExpressionNodePromoter->reveal(),
            $this->bagNodePromoter->reveal(),
            $this->modeContext->reveal()
        );
    }

    public function testPromoteFunctionExpressionIsCorrectForDevelopmentMode()
    {
        $resultType = new StaticType(TextExpression::class);
        $argumentBagNode = $this->prophesize(ExpressionBagNode::class);
        /** @var ObjectProphecy|FunctionExpressionNode $node */
        $node = $this->prophesize(FunctionExpressionNode::class);
        $node->getType()->willReturn(FunctionExpressionNode::TYPE);
        $node->getLibraryName()->willReturn('my_lib');
        $node->getFunctionName()->willReturn('my_func');
        $node->getArgumentExpressionBag()->willReturn($argumentBagNode);
        $node->getResolvedResultType()->willReturn($resultType);
        $argumentBag = $this->prophesize(ExpressionBag::class);
        $this->bagNodePromoter->promoteExpressionBag(Argument::exact($argumentBagNode))
            ->willReturn($argumentBag);
        $functionExpression = $this->prophesize(FunctionExpression::class);

        $this->expressionFactory->createFunctionExpression(Argument::cetera())
            ->will($this->noBind(function (array $args) use ($argumentBag, $functionExpression, $resultType) {
                $this->assert($args[0])->exactlyEquals('my_lib');
                $this->assert($args[1])->exactlyEquals('my_func');
                $this->assert($args[2])->exactlyEquals($argumentBag->reveal());
                // Use the resolved result type for the function using its arguments for this call
                $this->assert($args[3])->exactlyEquals($resultType);
                return $functionExpression;
            }))
            ->shouldBeCalledOnce();
        $this->assert($this->promoter->promote($node->reveal()))->exactlyEquals($functionExpression->reveal());
    }

    public function testPromoteFunctionExpressionIsCorrectForProductionMode()
    {
        $this->modeContext->getMode()->willReturn(new ProductionMode());
        $argumentBagNode = $this->prophesize(ExpressionBagNode::class);
        /** @var ObjectProphecy|FunctionExpressionNode $node */
        $node = $this->prophesize(FunctionExpressionNode::class);
        $node->getType()->willReturn(FunctionExpressionNode::TYPE);
        $node->getLibraryName()->willReturn('my_lib');
        $node->getFunctionName()->willReturn('my_func');
        $node->getArgumentExpressionBag()->willReturn($argumentBagNode);
        $argumentBag = $this->prophesize(ExpressionBag::class);
        $this->bagNodePromoter->promoteExpressionBag(Argument::exact($argumentBagNode))
            ->willReturn($argumentBag);
        $functionExpression = $this->prophesize(FunctionExpression::class);

        $this->expressionFactory->createFunctionExpression(Argument::cetera())
            ->will($this->noBind(function (array $args) use ($argumentBag, $functionExpression) {
                $this->assert($args[0])->exactlyEquals('my_lib');
                $this->assert($args[1])->exactlyEquals('my_func');
                $this->assert($args[2])->exactlyEquals($argumentBag->reveal());
                // Allow any return type in production mode
                $this->assert($args[3])->isAnInstanceOf(AnyType::class);
                return $functionExpression;
            }))
            ->shouldBeCalledOnce();
        $this->assert($this->promoter->promote($node->reveal()))->exactlyEquals($functionExpression->reveal());
    }
}
