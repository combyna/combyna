<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Expression\Config\Loader;

use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\UnknownExpressionTypeNode;
use Combyna\Component\Expression\Config\Loader\DelegatingExpressionLoader;
use Combyna\Component\Expression\Config\Loader\ExpressionTypeLoaderInterface;
use Combyna\Component\ExpressionLanguage\Config\Act\UnparsableExpressionNode;
use Combyna\Component\ExpressionLanguage\Exception\ParseFailedException;
use Combyna\Component\ExpressionLanguage\ExpressionParser;
use Combyna\Component\ExpressionLanguage\ExpressionParserInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class DelegatingExpressionLoaderTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingExpressionLoaderTest extends TestCase
{
    /**
     * @var ObjectProphecy|ExpressionParser
     */
    private $expressionParser;

    /**
     * @var ObjectProphecy|ExpressionTypeLoaderInterface
     */
    private $firstSubLoader;

    /**
     * @var DelegatingExpressionLoader
     */
    private $loader;

    /**
     * @var ObjectProphecy|ExpressionTypeLoaderInterface
     */
    private $secondSubLoader;

    /**
     * @var ObjectProphecy|ExpressionTypeLoaderInterface
     */
    private $unparsableExpressionSubLoader;

    public function setUp()
    {
        $this->expressionParser = $this->prophesize(ExpressionParserInterface::class);
        $this->firstSubLoader = $this->prophesize(ExpressionTypeLoaderInterface::class);
        $this->secondSubLoader = $this->prophesize(ExpressionTypeLoaderInterface::class);
        $this->unparsableExpressionSubLoader = $this->prophesize(ExpressionTypeLoaderInterface::class);

        $this->firstSubLoader->getType()
            ->willReturn('first_type');

        $this->secondSubLoader->getType()
            ->willReturn('second_type');

        $this->unparsableExpressionSubLoader->getType()
            ->willReturn(UnparsableExpressionNode::TYPE);

        $this->loader = new DelegatingExpressionLoader($this->expressionParser->reveal());

        $this->loader->addLoader($this->firstSubLoader->reveal());
        $this->loader->addLoader($this->secondSubLoader->reveal());
        $this->loader->addLoader($this->unparsableExpressionSubLoader->reveal());
    }

    public function testLoadReturnsTheExpressionNodeFromSubLoaderForAnArrayConfig()
    {
        $node = $this->prophesize(ExpressionNodeInterface::class);
        $this->firstSubLoader->load(['type' => 'first_type', 'my_arg' => 21])
            ->willReturn($node);

        static::assertSame($node->reveal(), $this->loader->load(['type' => 'first_type', 'my_arg' => 21]));
    }

    public function testLoadReturnsTheExpressionNodeFromSubLoaderAfterParsingFormula()
    {
        $this->expressionParser->parse('21 * 4')
            ->willReturn(['type' => 'first_type', 'left' => 21, 'right' => 4]);
        $node = $this->prophesize(ExpressionNodeInterface::class);
        $this->firstSubLoader->load(['type' => 'first_type', 'left' => 21, 'right' => 4])
            ->willReturn($node);

        static::assertSame($node->reveal(), $this->loader->load('=21 * 4'));
    }

    public function testLoadTrimsWhitespaceFromExpressionFormula()
    {
        $this->expressionParser->parse('21 * 4')
            ->willReturn(['type' => 'first_type', 'left' => 21, 'right' => 4]);
        $node = $this->prophesize(ExpressionNodeInterface::class);
        $this->firstSubLoader->load(['type' => 'first_type', 'left' => 21, 'right' => 4])
            ->willReturn($node);

        // Note that expression formula string is surrounded by whitespace
        static::assertSame($node->reveal(), $this->loader->load('    =21 * 4    '));
    }

    public function testLoadReturnsAnUnparsableExpressionNodeViaItsSubLoaderWhenStringWithoutFormulaPrefix()
    {
        $node = $this->prophesize(ExpressionNodeInterface::class);
        $this->unparsableExpressionSubLoader->load([
            'type' => UnparsableExpressionNode::TYPE,
            'expression' => 'not a valid formula'
        ])
            ->willReturn($node);

        static::assertSame($node->reveal(), $this->loader->load('not a valid formula'));
    }

    public function testLoadReturnsAnUnparsableExpressionNodeViaItsSubLoaderWhenFormulaParseFails()
    {
        $this->expressionParser->parse('not valid even with prefix')
            ->willThrow(new ParseFailedException('Unable to parse expression formula'));
        $node = $this->prophesize(ExpressionNodeInterface::class);
        $this->unparsableExpressionSubLoader->load([
            'type' => UnparsableExpressionNode::TYPE,
            'expression' => 'not valid even with prefix'
        ])
            ->willReturn($node);

        static::assertSame($node->reveal(), $this->loader->load('=not valid even with prefix'));
    }

    public function testLoadReturnsAnUnparsableExpressionNodeViaItsSubLoaderWhenNotArrayOrString()
    {
        $node = $this->prophesize(ExpressionNodeInterface::class);
        $this->unparsableExpressionSubLoader->load([
            'type' => UnparsableExpressionNode::TYPE,
            'expression' => 4567
        ])
            ->willReturn($node);

        static::assertSame($node->reveal(), $this->loader->load(4567));
    }

    public function testLoadReturnsAnUnknownExpressionTypeNodeWhenTypeElementIsMissing()
    {
        $node = $this->loader->load(['my_arg' => 21]);

        static::assertInstanceOf(UnknownExpressionTypeNode::class, $node);
        static::assertNull($node->getUnknownType());
    }

    public function testLoadReturnsAnUnknownExpressionTypeNodeWhenNoLoaderIsRegisteredForType()
    {
        $node = $this->loader->load(['type' => 'my_unknown_type', 'my_arg' => 21]);

        static::assertInstanceOf(UnknownExpressionTypeNode::class, $node);
        static::assertSame('my_unknown_type', $node->getUnknownType());
    }
}
