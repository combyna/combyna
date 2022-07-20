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
use Combyna\Component\Expression\TextExpression;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class TextExpressionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextExpressionTest extends TestCase
{
    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var TextExpression
     */
    private $expression;

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);

        $this->expression = new TextExpression('this is my string');
    }

    /**
     * @dataProvider stringProvider
     * @param string $string
     */
    public function testConstructorAllowsValidStrings($string)
    {
        $this->expectNotToPerformAssertions();

        new TextExpression($string);
    }

    /**
     * @return array
     */
    public function stringProvider()
    {
        return [
            'non-empty' => ['this is my text'],
            'empty' => ['']
        ];
    }

    /**
     * @dataProvider nonStringWithTypeProvider
     * @param mixed $nonString
     * @param string $type
     */
    public function testConstructorThrowsExceptionWhenNonStringGiven($nonString, $type)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'TextExpression expects a string, ' . $type . ' given'
        );

        new TextExpression($nonString);
    }

    /**
     * @return array
     */
    public function nonStringWithTypeProvider()
    {
        return [
            'int' => [21, 'integer'],
            'float' => [27.5, 'double'],
            'null' => [null, 'NULL'],
            'boolean' => [false, 'boolean']
        ];
    }

    public function testGetTypeReturnsTheTextType()
    {
        static::assertSame('text', $this->expression->getType());
    }

    public function testToNativeReturnsTheNativeTextString()
    {
        static::assertSame('this is my string', $this->expression->toNative());
    }

    public function testToStaticReturnsItself()
    {
        static::assertSame($this->expression, $this->expression->toStatic($this->evaluationContext->reveal()));
    }
}
