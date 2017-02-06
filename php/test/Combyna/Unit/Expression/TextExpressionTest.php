<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Expression;

use Combyna\Evaluation\EvaluationContextInterface;
use Combyna\Expression\TextExpression;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Combyna\Type\StaticType;
use InvalidArgumentException;
use Prophecy\Argument;
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

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->expression = new TextExpression('this is my string');
    }

    /**
     * @dataProvider stringProvider
     * @param string $string
     */
    public function testConstructorAllowsValidStrings($string)
    {
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
        $this->setExpectedException(
            InvalidArgumentException::class,
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

    public function testGetResultTypeReturnsAStaticTextType()
    {
        $resultType = $this->expression->getResultType($this->validationContext->reveal());

        $this->assert($resultType)->isAnInstanceOf(StaticType::class);
        $this->assert($resultType->getSummary())->exactlyEquals('text');
    }

    public function testGetTypeReturnsTheTextType()
    {
        $this->assert($this->expression->getType())->exactlyEquals('text');
    }

    public function testToNativeReturnsTheNativeTextString()
    {
        $this->assert($this->expression->toNative())->exactlyEquals('this is my string');
    }

    public function testToStaticReturnsItself()
    {
        $this->assert($this->expression->toStatic($this->evaluationContext->reveal()))
            ->exactlyEquals($this->expression);
    }
}
