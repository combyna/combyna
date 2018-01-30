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
        $this->assert($this->bag->getExpression('second-expr'))->exactlyEquals($this->expression2->reveal());
    }

    public function testGetExpressionThrowsWhenNoExpressionExistsInBagWithGivenName()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Expression bag contains no "an-undefined-expr" expression'
        );

        $this->bag->getExpression('an-undefined-expr');
    }

    public function testGetExpressionNamesReturnsNamesOfAllExpressionsInBag()
    {
        $this->assert($this->bag->getExpressionNames())->exactlyEquals(['first-expr', 'second-expr']);
    }

    public function testHasExpressionReturnsTrueWhenExpressionIsDefinedInBag()
    {
        $this->assert($this->bag->hasExpression('first-expr'))->isTrue;
    }

    public function testHasExpressionReturnsFalseWhenExpressionIsNotDefinedInBag()
    {
        $this->assert($this->bag->hasExpression('an-undefined-expr'))->isFalse;
    }

//    public function testSetExpressionAllowsAnExistingExpressionToBeReplaced()
//    {
//        /** @var ObjectProphecy|ExpressionInterface $newExpression */
//        $newExpression = $this->prophesize(ExpressionInterface::class);
//
//        $this->bag->setExpression('second-expr', $newExpression->reveal());
//
//        $this->assert($this->bag->getExpression('second-expr'))->exactlyEquals($newExpression->reveal());
//    }
//
//    public function testSetExpressionDoesNotAllowAnExpressionToBeAdded()
//    {
//        $this->setExpectedException(
//            InvalidArgumentException::class,
//            'Expression bag contains no "my-new-expr" expression'
//        );
//
//        $this->bag->setExpression('my-new-expr', $this->prophesize(ExpressionInterface::class)->reveal());
//    }
}
