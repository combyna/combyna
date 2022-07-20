<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Config\Parameter\Type;

use Combyna\Component\Config\Parameter\Type\ExpressionParameterType;
use Combyna\Component\Config\Parameter\Type\ExpressionParameterTypeParser;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

/**
 * Class ExpressionParameterTypeParserTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionParameterTypeParserTest extends TestCase
{
    /**
     * @var ObjectProphecy|ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @var ObjectProphecy|ExpressionParameterType
     */
    private $parameterType;

    /**
     * @var ExpressionParameterTypeParser
     */
    private $parser;

    public function setUp()
    {
        $this->expressionLoader = $this->prophesize(ExpressionLoaderInterface::class);
        $this->parameterType = $this->prophesize(ExpressionParameterType::class);

        $this->parser = new ExpressionParameterTypeParser($this->expressionLoader->reveal());
    }

    /**
     * @dataProvider argumentIsValid_dataProvider
     * @param mixed $value
     * @param bool $expectedResult
     */
    public function testArgumentIsValid($value, $expectedResult)
    {
        static::assertSame($expectedResult, $this->parser->argumentIsValid($this->parameterType->reveal(), $value));
    }

    public function argumentIsValid_dataProvider()
    {
        return [
            'array' => [['my array'], true],
            'boolean' => [false, false],
            'float' => [123.456, false],
            'integer' => [123, false],
            'null' => [null, false],
            'object' => [new stdClass(), false],
            'string' => ['my string', true],
        ];
    }

    public function testParseArgumentLoadsViaTheExpressionLoader()
    {
        /** @var ObjectProphecy|ExpressionNodeInterface $resultExpressionNode */
        $resultExpressionNode = $this->prophesize(ExpressionNodeInterface::class);
        $this->expressionLoader->load('my string')
            ->willReturn($resultExpressionNode);

        $result = $this->parser->parseArgument($this->parameterType->reveal(), 'my string');

        static::assertSame($resultExpressionNode->reveal(), $result);
    }
}
