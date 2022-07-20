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
use Combyna\Component\Validator\Context\NullValidationContext;
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
        $resultType = new StaticType(TextExpression::class, new NullValidationContext());
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
                static::assertSame('my_lib', $args[0]);
                static::assertSame('my_func', $args[1]);
                static::assertSame($argumentBag->reveal(), $args[2]);
                // Use the resolved result type for the function using its arguments for this call
                static::assertSame($resultType, $args[3]);
                return $functionExpression;
            }))
            ->shouldBeCalledOnce();
        static::assertSame($functionExpression->reveal(), $this->promoter->promote($node->reveal()));
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
                static::assertSame('my_lib', $args[0]);
                static::assertSame('my_func', $args[1]);
                static::assertSame($argumentBag->reveal(), $args[2]);
                // Allow any return type in production mode
                static::assertInstanceOf(AnyType::class, $args[3]);
                return $functionExpression;
            }))
            ->shouldBeCalledOnce();
        static::assertSame($functionExpression->reveal(), $this->promoter->promote($node->reveal()));
    }
}
