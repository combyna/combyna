<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Config\Loader;

use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Loader\ArgumentParser;
use Combyna\Component\Config\Parameter\ArgumentBag;
use Combyna\Component\Config\Parameter\ExtraParameter;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\ParameterParserInterface;
use Combyna\Component\Config\Parameter\Type\TextParameterType;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ArgumentParserTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ArgumentParserTest extends TestCase
{
    /**
     * @var ObjectProphecy|ParameterParserInterface
     */
    private $parameterParser;

    /**
     * @var ArgumentParser
     */
    private $parser;

    public function setUp()
    {
        $this->parameterParser = $this->prophesize(ParameterParserInterface::class);
        $this->parameterParser
            ->parseArgument(Argument::cetera())
            ->will(function (array $args) {
                list($parameter, $config, /*$parameterList*/, $extraArguments) = $args;

                if ($parameter instanceof ExtraParameter) {
                    return $extraArguments;
                }

                return $config[ArgumentParser::NAMED_ARGUMENTS][$parameter->getName()]['text'];
            });

        $this->parser = new ArgumentParser($this->parameterParser->reveal());
    }

    public function testParseArgumentsReturnsCorrectArgumentBagWhenOnlyExplicitNamedTextArguments()
    {
        $argumentBag = $this->parser->parseArguments([
            ArgumentParser::NAMED_ARGUMENTS => [
                'my_first_param' => ['type' => 'text', 'text' => 'first value'],
                'my_second_param' => ['type' => 'text', 'text' => 'second value']
            ]
        ], [
            new NamedParameter('my_first_param', new TextParameterType('first param')),
            new NamedParameter('my_second_param', new TextParameterType('second param'))
        ]);

        static::assertInstanceOf(ArgumentBag::class, $argumentBag);
        static::assertSame('first value', $argumentBag->getNamedStringArgument('my_first_param'));
        static::assertSame('second value', $argumentBag->getNamedStringArgument('my_second_param'));
        static::assertEquals([], $argumentBag->getExtraArguments());
    }

    public function testParseArgumentsReturnsCorrectArgumentBagWhenExtraArgumentsAreAllowed()
    {
        $argumentBag = $this->parser->parseArguments([
            ArgumentParser::NAMED_ARGUMENTS => [
                'my_explicit_param' => ['type' => 'text', 'text' => 'explicit value'],
                'some_other_arg' => 21,
                'another_arg' => 1001
            ]
        ], [
            new NamedParameter('my_explicit_param', new TextParameterType('explicit param')),
            new ExtraParameter()
        ]);

        static::assertInstanceOf(ArgumentBag::class, $argumentBag);
        static::assertSame('explicit value', $argumentBag->getNamedStringArgument('my_explicit_param'));
        static::assertEquals([
            'some_other_arg' => 21,
            'another_arg' => 1001
        ], $argumentBag->getExtraArguments());
    }

    public function testParseArgumentsThrowsExceptionWhenExtraArgumentsAreDisallowed()
    {
        $this->expectException(ArgumentParseException::class);
        $this->expectExceptionMessage(
            'Extra arguments not allowed: [some_extra_arg]'
        );

        $this->parser->parseArguments([
            ArgumentParser::NAMED_ARGUMENTS => [
                'my_explicit_param' => ['type' => 'text', 'text' => 'explicit value'],
                'some_extra_arg' => 21
            ]
        ], [
            new NamedParameter('my_explicit_param', new TextParameterType('explicit param'))
        ]);
    }
}
