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

use Combyna\Component\Expression\Config\Act\NothingExpressionNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class NothingExpressionNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NothingExpressionNodeTest extends TestCase
{
    /**
     * @var NothingExpressionNode
     */
    private $node;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->node = new NothingExpressionNode();
    }

    public function testGetTypeReturnsTheNothingType()
    {
        static::assertSame('nothing', $this->node->getType());
    }

    public function testToNativeReturnsTheNull()
    {
        static::assertNull($this->node->toNative());
    }
}
