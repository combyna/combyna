<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Bag;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\ExpressionBag;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ExpressionBag
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionBagTest extends TestCase
{
    /**
     * @var ExpressionBag
     */
    private $bag;

    /**
     * @var ObjectProphecy|BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var ObjectProphecy|ExpressionInterface
     */
    private $expression1;

    /**
     * @var ObjectProphecy|ExpressionInterface
     */
    private $expression2;

    public function setUp()
    {
        $this->bagFactory = $this->prophesize(BagFactoryInterface::class);
        $this->expression1 = $this->prophesize(ExpressionInterface::class);
        $this->expression2 = $this->prophesize(ExpressionInterface::class);

        $this->bag = new ExpressionBag($this->bagFactory->reveal(), [
            'first-expr' => $this->expression1->reveal(),
            'second-expr' => $this->expression2->reveal()
        ]);
    }

    public function testGetExpressionReturnsTheCorrectExpression()
    {
        static::assertSame($this->expression2->reveal(), $this->bag->getExpression('second-expr'));
    }

    public function testGetExpressionThrowsWhenNoExpressionExistsInBagWithGivenName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Expression bag contains no "an-undefined-expr" expression'
        );

        $this->bag->getExpression('an-undefined-expr');
    }

    public function testGetExpressionNamesReturnsNamesOfAllExpressionsInBag()
    {
        static::assertSame(['first-expr', 'second-expr'], $this->bag->getExpressionNames());
    }

    public function testHasExpressionReturnsTrueWhenExpressionIsDefinedInBag()
    {
        static::assertTrue($this->bag->hasExpression('first-expr'));
    }

    public function testHasExpressionReturnsFalseWhenExpressionIsNotDefinedInBag()
    {
        static::assertFalse($this->bag->hasExpression('an-undefined-expr'));
    }
}
