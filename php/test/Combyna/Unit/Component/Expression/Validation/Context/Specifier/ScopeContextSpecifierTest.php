<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Expression\Validation\Context\Specifier;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Bag\Config\Act\FixedStaticDefinitionNodeInterface;
use Combyna\Component\Expression\Validation\Context\Specifier\ScopeContextSpecifier;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;
use Combyna\Harness\TestCase;
use InvalidArgumentException;

/**
 * Class ScopeContextSpecifierTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ScopeContextSpecifierTest extends TestCase
{
    /**
     * @var ScopeContextSpecifier
     */
    private $specifier;

    public function setUp()
    {
        $this->specifier = new ScopeContextSpecifier();
    }

    public function testDefineBagStaticsAsVariablesAddsVariableTypeDeterminers()
    {
        $typeDeterminerFromVariable = $this->prophesize(TypeDeterminerInterface::class);
        $typeDeterminerFromFirstStatic = $this->prophesize(TypeDeterminerInterface::class);
        $typeDeterminerFromSecondStatic = $this->prophesize(TypeDeterminerInterface::class);
        $firstStaticDefinition = $this->prophesize(FixedStaticDefinitionNodeInterface::class);
        $secondStaticDefinition = $this->prophesize(FixedStaticDefinitionNodeInterface::class);
        $fixedStaticBagModelNode = $this->prophesize(FixedStaticBagModelNodeInterface::class);
        $firstStaticDefinition->getStaticTypeDeterminer()
            ->willReturn($typeDeterminerFromFirstStatic);
        $secondStaticDefinition->getStaticTypeDeterminer()
            ->willReturn($typeDeterminerFromSecondStatic);
        $fixedStaticBagModelNode->getStaticDefinitions()
            ->willReturn([
                'first-static' => $firstStaticDefinition->reveal(),
                'second-static' => $secondStaticDefinition->reveal()
            ]);

        $this->specifier->defineVariable('from-var', $typeDeterminerFromVariable->reveal());
        $this->specifier->defineBagStaticsAsVariables($fixedStaticBagModelNode->reveal());

        self::assertSame(
            [
                'from-var' => $typeDeterminerFromVariable->reveal(),
                'first-static' => $typeDeterminerFromFirstStatic->reveal(),
                'second-static' => $typeDeterminerFromSecondStatic->reveal()
            ],
            $this->specifier->getVariableTypeDeterminers()
        );
    }

    public function testDefineBagStaticsAsVariablesThrowsWhenScopeAlreadyDefinesTheVariable()
    {
        $staticDefinition = $this->prophesize(FixedStaticDefinitionNodeInterface::class);
        $newTypeDeterminerFromStatic = $this->prophesize(TypeDeterminerInterface::class);
        $fixedStaticBagModelNode = $this->prophesize(FixedStaticBagModelNodeInterface::class);
        $staticDefinition->getStaticTypeDeterminer()
            ->willReturn($newTypeDeterminerFromStatic);
        $fixedStaticBagModelNode->getStaticDefinitions()
            ->willReturn([
                'your-var' => $staticDefinition->reveal()
            ]);
        $oldTypeDeterminerFromVariable = $this->prophesize(TypeDeterminerInterface::class);
        $this->specifier->defineVariable('your-var', $oldTypeDeterminerFromVariable->reveal());

        $this->setExpectedException(
            InvalidArgumentException::class,
            'Scope already has a variable "your-var"'
        );

        $this->specifier->defineBagStaticsAsVariables($fixedStaticBagModelNode->reveal());
    }

    public function testDefineVariableAddsVariableTypeDeterminers()
    {
        $firstVariableTypeDeterminer = $this->prophesize(TypeDeterminerInterface::class);
        $secondVariableTypeDeterminer = $this->prophesize(TypeDeterminerInterface::class);

        $this->specifier->defineVariable('first-var', $firstVariableTypeDeterminer->reveal());
        $this->specifier->defineVariable('second-var', $secondVariableTypeDeterminer->reveal());

        self::assertSame(
            [
                'first-var' => $firstVariableTypeDeterminer->reveal(),
                'second-var' => $secondVariableTypeDeterminer->reveal()
            ],
            $this->specifier->getVariableTypeDeterminers()
        );
    }

    public function testDefineVariableThrowsWhenScopeAlreadyDefinesTheVariable()
    {
        $oldVariableTypeDeterminer = $this->prophesize(TypeDeterminerInterface::class);
        $this->specifier->defineVariable('my-var', $oldVariableTypeDeterminer->reveal());

        $this->setExpectedException(
            InvalidArgumentException::class,
            'Scope already has a variable "my-var"'
        );

        // Attempt to define the same variable again
        $newVariableTypeDeterminer = $this->prophesize(TypeDeterminerInterface::class);
        $this->specifier->defineVariable('my-var', $newVariableTypeDeterminer->reveal());
    }
}
