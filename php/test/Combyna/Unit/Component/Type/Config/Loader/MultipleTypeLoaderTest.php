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
use Combyna\Component\Type\Config\Loader\MultipleTypeLoader;
use Combyna\Component\Type\Config\Loader\TypeLoaderInterface;
use Combyna\Component\Type\MultipleType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\MultipleTypeDeterminer;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class MultipleTypeLoaderTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MultipleTypeLoaderTest extends TestCase
{
    /**
     * @var ObjectProphecy|ConfigParser
     */
    private $configParser;

    /**
     * @var ObjectProphecy|TypeLoaderInterface
     */
    private $delegatingTypeLoader;

    /**
     * @var MultipleTypeLoader
     */
    private $loader;

    /**
     * @var ObjectProphecy|TypeDeterminerInterface
     */
    private $resultingTypeDeterminer1;

    /**
     * @var ObjectProphecy|TypeDeterminerInterface
     */
    private $resultingTypeDeterminer2;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->configParser = $this->prophesize(ConfigParser::class);
        $this->delegatingTypeLoader = $this->prophesize(TypeLoaderInterface::class);
        $this->resultingTypeDeterminer1 = $this->prophesize(TypeDeterminerInterface::class);
        $this->resultingTypeDeterminer2 = $this->prophesize(TypeDeterminerInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->configParser->getElement(Argument::type('array'), 'types', 'sub-types', ['array'])
            ->will(function (array $args) {
                return $args[0]['types'];
            });

        $this->loader = new MultipleTypeLoader($this->configParser->reveal(), $this->delegatingTypeLoader->reveal());
    }

    public function testGetTypesReturnsOnlyTheMultipleTypeName()
    {
        $this->assert($this->loader->getTypes())->exactlyEquals(['multiple']);
    }

    public function testLoadReturnsAMultipleTypeDeterminerWithAllSubTypesLoaded()
    {
        /** @var ObjectProphecy|TypeInterface $subType1 */
        $subType1 = $this->prophesize(TypeInterface::class);
        /** @var ObjectProphecy|TypeInterface $subType2 */
        $subType2 = $this->prophesize(TypeInterface::class);
        $subType1->getSummary()->willReturn('my-type-summary');
        $subType2->getSummary()->willReturn('your-type-summary');
        $this->resultingTypeDeterminer1->determine($this->validationContext)
            ->willReturn($subType1);
        $this->resultingTypeDeterminer2->determine($this->validationContext)
            ->willReturn($subType2);
        $this->delegatingTypeLoader->load('my_type')
            ->willReturn($this->resultingTypeDeterminer1->reveal());
        $this->delegatingTypeLoader->load('your_type')
            ->willReturn($this->resultingTypeDeterminer2->reveal());

        $determiner = $this->loader->load(['type' => 'multiple', 'types' => ['my_type', 'your_type']]);

        $this->assert($determiner)->isAnInstanceOf(MultipleTypeDeterminer::class);
        $multipleType = $determiner->determine($this->validationContext->reveal());
        $this->assert($multipleType)->isAnInstanceOf(MultipleType::class);
        $this->assert($multipleType->getSummary())->exactlyEquals('my-type-summary|your-type-summary');
    }

    public function testLoadReturnsPresolvedUnresolvedTypeWhenConfigParserThrowsException()
    {
        $this->configParser->getElement(Argument::type('array'), 'types', 'sub-types', ['array'])
            ->willThrow(new InvalidArgumentException('Some issue parsing sub-types'));

        $determiner = $this->loader->load(['some_arg' => 21]);

        $this->assert($determiner)->isAnInstanceOf(PresolvedTypeDeterminer::class);
        $this->assert($determiner->determine($this->validationContext->reveal())->getSummary())
            ->exactlyEquals('unknown<Some issue parsing sub-types>');
    }
}
