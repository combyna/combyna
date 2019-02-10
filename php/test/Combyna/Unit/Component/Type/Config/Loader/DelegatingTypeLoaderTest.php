<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Type\Config\Loader;

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Type\Config\Loader\DelegatingTypeLoader;
use Combyna\Component\Type\Config\Loader\TypeTypeLoaderInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class DelegatingTypeLoaderTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingTypeLoaderTest extends TestCase
{
    /**
     * @var ObjectProphecy|ConfigParser
     */
    private $configParser;

    /**
     * @var DelegatingTypeLoader
     */
    private $loader;

    /**
     * @var ObjectProphecy|TypeTypeLoaderInterface
     */
    private $multipleTypeLoader;

    /**
     * @var ObjectProphecy|TypeTypeLoaderInterface
     */
    private $myTypeLoader;

    /**
     * @var ObjectProphecy|TypeDeterminerInterface
     */
    private $resultingTypeDeterminer;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->configParser = $this->prophesize(ConfigParser::class);
        $this->multipleTypeLoader = $this->prophesize(TypeTypeLoaderInterface::class);
        $this->myTypeLoader = $this->prophesize(TypeTypeLoaderInterface::class);
        $this->resultingTypeDeterminer = $this->prophesize(TypeDeterminerInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->configParser->getElement(Argument::type('array'), 'type', 'type name')
            ->will(function (array $args) {
                return $args[0]['type'];
            });
        $this->multipleTypeLoader->getTypes()->willReturn(['multiple']);
        $this->myTypeLoader->getTypes()->willReturn(['my_type']);

        $this->loader = new DelegatingTypeLoader($this->configParser->reveal());
        $this->loader->addLoader($this->multipleTypeLoader->reveal());
        $this->loader->addLoader($this->myTypeLoader->reveal());
    }

    public function testLoadDelegatesToTheDelegateeWhenAConfigArrayIsGiven()
    {
        $this->myTypeLoader->load(['type' => 'my_type', 'some_arg' => 21])
            ->willReturn($this->resultingTypeDeterminer->reveal());

        $this->assert($this->loader->load(['type' => 'my_type', 'some_arg' => 21]))
            ->exactlyEquals($this->resultingTypeDeterminer->reveal());
    }

    public function testLoadDelegatesToTheDelegateeWhenJustATypeNameIsGiven()
    {
        $this->myTypeLoader->load(['type' => 'my_type'])
            ->willReturn($this->resultingTypeDeterminer->reveal());

        $this->assert($this->loader->load('my_type'))
            ->exactlyEquals($this->resultingTypeDeterminer->reveal());
    }

    public function testLoadDelegatesToTheMultipleTypeLoaderWhenAPipeSeparatedShorthandIsGiven()
    {
        $this->multipleTypeLoader->load(['type' => 'multiple', 'types' => ['my_type', 'your_type']])
            ->willReturn($this->resultingTypeDeterminer->reveal());

        $this->assert($this->loader->load('my_type|your_type'))
            ->exactlyEquals($this->resultingTypeDeterminer->reveal());
    }

    public function testLoadReturnsPresolvedUnresolvedTypeWhenNoRelevantTypeLoaderIsRegistered()
    {
        $determiner = $this->loader->load('some_undefined_type');

        $this->assert($determiner)->isAnInstanceOf(PresolvedTypeDeterminer::class);
        $this->assert($determiner->determine($this->validationContext->reveal())->getSummary())
            ->exactlyEquals('unknown<No loader is registered for types of type "some_undefined_type">');
    }

    public function testLoadReturnsPresolvedUnresolvedTypeWhenConfigParserThrowsException()
    {
        $this->configParser->getElement(Argument::type('array'), 'type', 'type name')
            ->willThrow(new InvalidArgumentException('Some issue parsing type name'));

        $determiner = $this->loader->load(['some_arg' => 21]);

        $this->assert($determiner)->isAnInstanceOf(PresolvedTypeDeterminer::class);
        $this->assert($determiner->determine($this->validationContext->reveal())->getSummary())
            ->exactlyEquals('unknown<Some issue parsing type name>');
    }
}
