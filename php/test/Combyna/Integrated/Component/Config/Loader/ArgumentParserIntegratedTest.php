<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Component\Config\Loader;

use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Loader\ArgumentParser;
use Combyna\Component\Config\Parameter\ArgumentBag;
use Combyna\Component\Config\Parameter\ExtraParameter;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\OptionalParameter;
use Combyna\Component\Config\Parameter\Type\ExpressionParameterType;
use Combyna\Component\Config\Parameter\Type\TextParameterType;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ArgumentParserIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ArgumentParserIntegratedTest extends TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ArgumentParser
     */
    private $parser;

    public function setUp()
    {
        global $combynaBootstrap;
        $this->container = $combynaBootstrap->getContainer();

        $this->parser = $this->container->get('combyna.config.loader.argument_parser');
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

        $this->assert($argumentBag)->isAnInstanceOf(ArgumentBag::class);
        $this->assert($argumentBag->getNamedTextArgument('my_first_param'))->exactlyEquals('first value');
        $this->assert($argumentBag->getNamedTextArgument('my_second_param'))->exactlyEquals('second value');
        $this->assert($argumentBag->getExtraArguments())->equals([]);
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

        $this->assert($argumentBag)->isAnInstanceOf(ArgumentBag::class);
        $this->assert($argumentBag->getNamedTextArgument('my_explicit_param'))->exactlyEquals('explicit value');
        $this->assert($argumentBag->getExtraArguments())->equals([
            'some_other_arg' => 21,
            'another_arg' => 1001
        ]);
    }

    public function testParseArgumentsThrowsExceptionWhenExtraArgumentsAreDisallowed()
    {
        $this->setExpectedException(
            ArgumentParseException::class,
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

    public function testParseArgumentsReturnsDefaultForOmittedOptionalArguments()
    {
        $argumentBag = $this->parser->parseArguments([
            ArgumentParser::NAMED_ARGUMENTS => [
                'my_required_param' => ['type' => 'text', 'text' => 'required value']
            ]
        ], [
            new NamedParameter('my_required_param', new TextParameterType('required param')),
            new OptionalParameter(
                new NamedParameter(
                    'my_optional_param',
                    new TextParameterType('optional param')
                ),
                'the default value'
            )
        ]);

        $this->assert($argumentBag)->isAnInstanceOf(ArgumentBag::class);
        $this->assert($argumentBag->getNamedTextArgument('my_required_param'))->exactlyEquals('required value');
        $this->assert($argumentBag->getNamedTextArgument('my_optional_param'))->exactlyEquals('the default value');
        $this->assert($argumentBag->getExtraArguments())->equals([]);
    }

    public function testParseArgumentsReturnsThePassedArgumentForASpecifiedOptionalArguments()
    {
        $argumentBag = $this->parser->parseArguments([
            ArgumentParser::NAMED_ARGUMENTS => [
                'my_required_param' => ['type' => 'text', 'text' => 'required value'],
                'my_optional_param' => ['type' => 'text', 'text' => 'optional but specified value']
            ]
        ], [
            new NamedParameter('my_required_param', new TextParameterType('required param')),
            new OptionalParameter(
                new NamedParameter(
                    'my_optional_param',
                    new TextParameterType('optional param')
                ),
                'the default value'
            )
        ]);

        $this->assert($argumentBag)->isAnInstanceOf(ArgumentBag::class);
        $this->assert($argumentBag->getNamedTextArgument('my_required_param'))->exactlyEquals('required value');
        $this->assert($argumentBag->getNamedTextArgument('my_optional_param'))->exactlyEquals('optional but specified value');
        $this->assert($argumentBag->getExtraArguments())->equals([]);
    }

    public function testParseArgumentsThrowsExceptionWhenTextArgumentIsOfIncorrectTypeForTextParameter()
    {
        $this->setExpectedException(
            ArgumentParseException::class,
            'Wrong type of value given for argument "my_param": expected text for a param, got integer(21111)'
        );

        $this->parser->parseArguments([
            ArgumentParser::NAMED_ARGUMENTS => [
                'my_param' => 21111
            ]
        ], [
            new NamedParameter('my_param', new TextParameterType('a param'))
        ]);
    }

    public function testParseArgumentsThrowsExceptionWhenTextArgumentIsOfIncorrectTypeForExpressionParameter()
    {
        $this->setExpectedException(
            ArgumentParseException::class,
            'Wrong type of value given for argument "my_param": expected an expression for a param, got integer(21111)'
        );

        $this->parser->parseArguments([
            ArgumentParser::NAMED_ARGUMENTS => [
                'my_param' => 21111
            ]
        ], [
            new NamedParameter('my_param', new ExpressionParameterType('a param'))
        ]);
    }
}
