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

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\NothingExpression;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class NothingExpressionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NothingExpressionTest extends TestCase
{
    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var NothingExpression
     */
    private $expression;

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);

        $this->expression = new NothingExpression();
    }

    public function testGetTypeReturnsTheNothingType()
    {
        static::assertSame('nothing', $this->expression->getType());
    }

    public function testToNativeReturnsNull()
    {
        static::assertNull($this->expression->toNative());
    }

    public function testToStaticReturnsItself()
    {
        static::assertSame($this->expression, $this->expression->toStatic($this->evaluationContext->reveal()));
    }
}
