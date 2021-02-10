<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Bag\Config\Act;

use Combyna\Component\Bag\Config\Act\DeterminedFixedStaticBagModelNode;
use Combyna\Component\Bag\Config\Act\DeterminedFixedStaticDefinitionNode;
use Combyna\Component\Bag\Config\Act\UnknownFixedStaticDefinitionNode;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class DeterminedFixedStaticBagModelNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DeterminedFixedStaticBagModelNodeTest extends TestCase
{
    /**
     * @var DeterminedFixedStaticBagModelNode
     */
    private $bagModelNode;

    /**
     * @var ObjectProphecy|DeterminedFixedStaticDefinitionNode
     */
    private $staticDefinitionNode1;

    /**
     * @var ObjectProphecy|DeterminedFixedStaticDefinitionNode
     */
    private $staticDefinitionNode2;

    public function setUp()
    {
        $this->staticDefinitionNode1 = $this->prophesize(DeterminedFixedStaticDefinitionNode::class);
        $this->staticDefinitionNode2 = $this->prophesize(DeterminedFixedStaticDefinitionNode::class);

        $this->staticDefinitionNode1->getName()
            ->willReturn('optional-static');
        $this->staticDefinitionNode1->getStaticTypeSummary()
            ->willReturn('text');
        $this->staticDefinitionNode1->isRequired()
            ->willReturn(false);
        $this->staticDefinitionNode1->allowsStaticDefinition(Argument::any())
            ->willReturn(false);
        $this->staticDefinitionNode2->getName()
            ->willReturn('required-static');
        $this->staticDefinitionNode2->getStaticTypeSummary()
            ->willReturn('number');
        $this->staticDefinitionNode2->isRequired()
            ->willReturn(true);
        $this->staticDefinitionNode2->allowsStaticDefinition(Argument::any())
            ->willReturn(false);

        $this->bagModelNode = new DeterminedFixedStaticBagModelNode([
            'optional-static' => $this->staticDefinitionNode1->reveal(),
            'required-static' => $this->staticDefinitionNode2->reveal()
        ]);
    }

    public function testAllowsOtherModelReturnsTrueWhenThereAreNoDefinitionsInEitherModel()
    {
        $bagModelNode = new DeterminedFixedStaticBagModelNode([]);
        $otherBagModelNode = new DeterminedFixedStaticBagModelNode([]);

        static::assertTrue($bagModelNode->allowsOtherModel($otherBagModelNode));
    }

    public function testAllowsOtherModelReturnsTrueWhenAllRequiredStaticsInThisModelAreInTheOther()
    {
        $otherStaticDefinitionNode = $this->prophesize(DeterminedFixedStaticDefinitionNode::class);
        $otherStaticDefinitionNode->getName()
            ->willReturn('required-static');
        $otherStaticDefinitionNode->getStaticTypeSummary()
            ->willReturn('number');
        $otherStaticDefinitionNode->isRequired()
            ->willReturn(false); // Doesn't need to _actually_ be required in the other model
        $this->staticDefinitionNode2
            ->allowsStaticDefinition($otherStaticDefinitionNode)
            ->willReturn(true);
        $otherBagModelNode = new DeterminedFixedStaticBagModelNode([
            $otherStaticDefinitionNode->reveal()
        ]);

        static::assertTrue($this->bagModelNode->allowsOtherModel($otherBagModelNode));
    }

    public function testAllowsOtherModelReturnsFalseWhenOneRequiredStaticInThisModelIsNotInTheOther()
    {
        $otherStaticDefinitionNode = $this->prophesize(DeterminedFixedStaticDefinitionNode::class);
        $otherStaticDefinitionNode->getName()
            ->willReturn('optional-static');
        $otherStaticDefinitionNode->getStaticTypeSummary()
            ->willReturn('text');
        $otherStaticDefinitionNode->isRequired()
            ->willReturn(false);
        $this->staticDefinitionNode1
            ->allowsStaticDefinition($otherStaticDefinitionNode)
            ->willReturn(true);
        $otherBagModelNode = new DeterminedFixedStaticBagModelNode([
            $otherStaticDefinitionNode->reveal()
        ]);

        static::assertFalse($this->bagModelNode->allowsOtherModel($otherBagModelNode));
    }

    public function testAllowsOtherModelReturnsFalseWhenTheOtherModelContainsARequiredStaticNotInThisOne()
    {
        $otherRequiredStaticDefinitionNode = $this->prophesize(DeterminedFixedStaticDefinitionNode::class);
        $otherRequiredStaticDefinitionNode->getName()
            ->willReturn('required-static');
        $otherRequiredStaticDefinitionNode->getStaticTypeSummary()
            ->willReturn('number');
        $otherRequiredStaticDefinitionNode->isRequired()
            ->willReturn(false); // Doesn't need to _actually_ be required in the other model
        $this->staticDefinitionNode2
            ->allowsStaticDefinition($otherRequiredStaticDefinitionNode)
            ->willReturn(true);
        // A required static that the other model defines but this one does not
        $anotherStaticDefinitionNode = $this->prophesize(DeterminedFixedStaticDefinitionNode::class);
        $anotherStaticDefinitionNode->getName()
            ->willReturn('another-static');
        $anotherStaticDefinitionNode->getStaticTypeSummary()
            ->willReturn('number');
        $anotherStaticDefinitionNode->isRequired()
            ->willReturn(true);
        $otherBagModelNode = new DeterminedFixedStaticBagModelNode([
            $otherRequiredStaticDefinitionNode->reveal(),
            $anotherStaticDefinitionNode->reveal()
        ]);

        static::assertFalse($this->bagModelNode->allowsOtherModel($otherBagModelNode));
    }

    public function testAllowsOtherModelReturnsFalseWhenTheOtherModelContainsAnOptionalStaticNotInThisOne()
    {
        $otherRequiredStaticDefinitionNode = $this->prophesize(DeterminedFixedStaticDefinitionNode::class);
        $otherRequiredStaticDefinitionNode->getName()
            ->willReturn('required-static');
        $otherRequiredStaticDefinitionNode->getStaticTypeSummary()
            ->willReturn('number');
        $otherRequiredStaticDefinitionNode->isRequired()
            ->willReturn(false); // Doesn't need to _actually_ be required in the other model
        $this->staticDefinitionNode2
            ->allowsStaticDefinition($otherRequiredStaticDefinitionNode)
            ->willReturn(true);
        // A required static that the other model defines but this one does not
        $anotherStaticDefinitionNode = $this->prophesize(DeterminedFixedStaticDefinitionNode::class);
        $anotherStaticDefinitionNode->getName()
            ->willReturn('another-static');
        $anotherStaticDefinitionNode->getStaticTypeSummary()
            ->willReturn('number');
        $anotherStaticDefinitionNode->isRequired()
            ->willReturn(false);
        $otherBagModelNode = new DeterminedFixedStaticBagModelNode([
            $otherRequiredStaticDefinitionNode->reveal(),
            $anotherStaticDefinitionNode->reveal()
        ]);

        static::assertFalse($this->bagModelNode->allowsOtherModel($otherBagModelNode));
    }

    public function testAllowsOtherModelReturnsFalseWhenTheOtherModelContainsAnOptionalStaticNotAllowedByItsEquivalentInThisOne()
    {
        $otherOptionalStaticDefinitionNode = $this->prophesize(DeterminedFixedStaticDefinitionNode::class);
        $otherOptionalStaticDefinitionNode->getName()
            ->willReturn('optional-static');
        $otherOptionalStaticDefinitionNode->getStaticTypeSummary()
            ->willReturn('number');
        $otherOptionalStaticDefinitionNode->isRequired()
            ->willReturn(false);
        $this->staticDefinitionNode1
            ->allowsStaticDefinition($otherOptionalStaticDefinitionNode)
            ->willReturn(false); // Don't allow `optional-static` to be accepted by this one (wrong type)
        $otherRequiredStaticDefinitionNode = $this->prophesize(DeterminedFixedStaticDefinitionNode::class);
        $otherRequiredStaticDefinitionNode->getName()
            ->willReturn('required-static');
        $otherRequiredStaticDefinitionNode->getStaticTypeSummary()
            ->willReturn('number');
        $otherRequiredStaticDefinitionNode->isRequired()
            ->willReturn(false); // Doesn't need to _actually_ be required in the other model
        $this->staticDefinitionNode2
            ->allowsStaticDefinition($otherRequiredStaticDefinitionNode)
            ->willReturn(true);
        $otherBagModelNode = new DeterminedFixedStaticBagModelNode([
            $otherOptionalStaticDefinitionNode->reveal(),
            $otherRequiredStaticDefinitionNode->reveal()
        ]);

        static::assertFalse($this->bagModelNode->allowsOtherModel($otherBagModelNode));
    }

    public function testAllowsOtherModelReturnsFalseWhenTheOtherModelContainsARequiredStaticNotAllowedByItsEquivalentInThisOne()
    {
        $otherOptionalStaticDefinitionNode = $this->prophesize(DeterminedFixedStaticDefinitionNode::class);
        $otherOptionalStaticDefinitionNode->getName()
            ->willReturn('optional-static');
        $otherOptionalStaticDefinitionNode->getStaticTypeSummary()
            ->willReturn('text');
        $otherOptionalStaticDefinitionNode->isRequired()
            ->willReturn(true);
        $this->staticDefinitionNode1
            ->allowsStaticDefinition($otherOptionalStaticDefinitionNode)
            ->willReturn(true);
        $otherRequiredStaticDefinitionNode = $this->prophesize(DeterminedFixedStaticDefinitionNode::class);
        $otherRequiredStaticDefinitionNode->getName()
            ->willReturn('required-static');
        $otherRequiredStaticDefinitionNode->getStaticTypeSummary()
            ->willReturn('text');
        $otherRequiredStaticDefinitionNode->isRequired()
            ->willReturn(false); // Doesn't need to _actually_ be required in the other model
        $this->staticDefinitionNode2
            ->allowsStaticDefinition($otherRequiredStaticDefinitionNode)
            ->willReturn(false); // Don't allow `required-static` to be accepted by this one (wrong type)
        $otherBagModelNode = new DeterminedFixedStaticBagModelNode([
            $otherOptionalStaticDefinitionNode->reveal(),
            $otherRequiredStaticDefinitionNode->reveal()
        ]);

        static::assertFalse($this->bagModelNode->allowsOtherModel($otherBagModelNode));
    }

    public function testGetStaticDefinitionByNameReturnsTheDefinitionWhenItExists()
    {
        $dynamicActNodeAdopter = $this->prophesize(DynamicActNodeAdopterInterface::class);

        static::assertSame(
            $this->staticDefinitionNode2->reveal(),
            $this->bagModelNode->getStaticDefinitionByName('required-static', $dynamicActNodeAdopter->reveal())
        );
    }

    public function testGetStaticDefinitionByNameReturnsUnknownFixedStaticDefinitionNodeWhenItDoesNotExist()
    {
        $dynamicActNodeAdopter = $this->prophesize(DynamicActNodeAdopterInterface::class);

        $result = $this->bagModelNode->getStaticDefinitionByName('my-undefined-static', $dynamicActNodeAdopter->reveal());

        static::assertInstanceOf(UnknownFixedStaticDefinitionNode::class, $result);
        static::assertSame('my-undefined-static', $result->getName());
    }

    public function testGetStaticDefinitionNamesReturnsAllNames()
    {
        static::assertEquals(
            ['optional-static', 'required-static'],
            $this->bagModelNode->getStaticDefinitionNames()
        );
    }

    public function testGetStaticDefinitionsReturnsAllDefinitionsIndexedByName()
    {
        static::assertSame(
            [
                'optional-static' => $this->staticDefinitionNode1->reveal(),
                'required-static' => $this->staticDefinitionNode2->reveal()
            ],
            $this->bagModelNode->getStaticDefinitions()
        );
    }

    public function testGetSummaryReturnsTheCorrectString()
    {
        static::assertSame('{optional-static: text, required-static: number}', $this->bagModelNode->getSummary());
    }
}
