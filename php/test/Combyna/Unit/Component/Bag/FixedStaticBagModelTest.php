<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Bag;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\FixedStaticBagModel;
use Combyna\Component\Bag\FixedStaticDefinition;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Harness\TestCase;
use LogicException;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class FixedStaticBagModelTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedStaticBagModelTest extends TestCase
{
    /**
     * @var FixedStaticBagModel
     */
    private $bagModel;

    /**
     * @var ObjectProphecy|BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var ObjectProphecy|FixedStaticDefinition
     */
    private $staticDefinition1;

    /**
     * @var ObjectProphecy|FixedStaticDefinition
     */
    private $staticDefinition2;

    public function setUp()
    {
        $this->bagFactory = $this->prophesize(BagFactoryInterface::class);
        $this->staticDefinition1 = $this->prophesize(FixedStaticDefinition::class);
        $this->staticDefinition2 = $this->prophesize(FixedStaticDefinition::class);

        $this->staticDefinition1->getName()
            ->willReturn('optional-static');
        $this->staticDefinition1->getStaticTypeSummary()
            ->willReturn('text');
        $this->staticDefinition1->isRequired()
            ->willReturn(false);
        $this->staticDefinition1->allowsStaticDefinition(Argument::any())
            ->willReturn(false);
        $this->staticDefinition2->getName()
            ->willReturn('required-static');
        $this->staticDefinition2->getStaticTypeSummary()
            ->willReturn('number');
        $this->staticDefinition2->isRequired()
            ->willReturn(true);
        $this->staticDefinition2->allowsStaticDefinition(Argument::any())
            ->willReturn(false);

        $this->bagModel = new FixedStaticBagModel($this->bagFactory->reveal(), [
            'optional-static' => $this->staticDefinition1->reveal(),
            'required-static' => $this->staticDefinition2->reveal()
        ]);
    }

    public function testAllowsOtherModelReturnsTrueWhenThereAreNoDefinitionsInEitherModel()
    {
        $bagModel = new FixedStaticBagModel($this->bagFactory->reveal(), []);
        $otherBagModel = new FixedStaticBagModel($this->bagFactory->reveal(), []);

        static::assertTrue($bagModel->allowsOtherModel($otherBagModel));
    }

    public function testAllowsOtherModelReturnsTrueWhenAllRequiredStaticsInThisModelAreInTheOther()
    {
        $otherStaticDefinition = $this->prophesize(FixedStaticDefinition::class);
        $otherStaticDefinition->getName()
            ->willReturn('required-static');
        $otherStaticDefinition->getStaticTypeSummary()
            ->willReturn('number');
        $otherStaticDefinition->isRequired()
            ->willReturn(false); // Doesn't need to _actually_ be required in the other model
        $this->staticDefinition2
            ->allowsStaticDefinition($otherStaticDefinition)
            ->willReturn(true);
        $otherBagModel = new FixedStaticBagModel($this->bagFactory->reveal(), [
            $otherStaticDefinition->reveal()
        ]);

        static::assertTrue($this->bagModel->allowsOtherModel($otherBagModel));
    }

    public function testAllowsOtherModelReturnsFalseWhenOneRequiredStaticInThisModelIsNotInTheOther()
    {
        $otherStaticDefinition = $this->prophesize(FixedStaticDefinition::class);
        $otherStaticDefinition->getName()
            ->willReturn('optional-static');
        $otherStaticDefinition->getStaticTypeSummary()
            ->willReturn('text');
        $otherStaticDefinition->isRequired()
            ->willReturn(false);
        $this->staticDefinition1
            ->allowsStaticDefinition($otherStaticDefinition)
            ->willReturn(true);
        $otherBagModel = new FixedStaticBagModel($this->bagFactory->reveal(), [
            $otherStaticDefinition->reveal()
        ]);

        static::assertFalse($this->bagModel->allowsOtherModel($otherBagModel));
    }

    public function testAllowsOtherModelReturnsFalseWhenTheOtherModelContainsARequiredStaticNotInThisOne()
    {
        $otherRequiredStaticDefinition = $this->prophesize(FixedStaticDefinition::class);
        $otherRequiredStaticDefinition->getName()
            ->willReturn('required-static');
        $otherRequiredStaticDefinition->getStaticTypeSummary()
            ->willReturn('number');
        $otherRequiredStaticDefinition->isRequired()
            ->willReturn(false); // Doesn't need to _actually_ be required in the other model
        $this->staticDefinition2
            ->allowsStaticDefinition($otherRequiredStaticDefinition)
            ->willReturn(true);
        // A required static that the other model defines but this one does not
        $anotherStaticDefinition = $this->prophesize(FixedStaticDefinition::class);
        $anotherStaticDefinition->getName()
            ->willReturn('another-static');
        $anotherStaticDefinition->getStaticTypeSummary()
            ->willReturn('number');
        $anotherStaticDefinition->isRequired()
            ->willReturn(true);
        $otherBagModel = new FixedStaticBagModel($this->bagFactory->reveal(), [
            $otherRequiredStaticDefinition->reveal(),
            $anotherStaticDefinition->reveal()
        ]);

        static::assertFalse($this->bagModel->allowsOtherModel($otherBagModel));
    }

    public function testAllowsOtherModelReturnsFalseWhenTheOtherModelContainsAnOptionalStaticNotInThisOne()
    {
        $otherRequiredStaticDefinition = $this->prophesize(FixedStaticDefinition::class);
        $otherRequiredStaticDefinition->getName()
            ->willReturn('required-static');
        $otherRequiredStaticDefinition->getStaticTypeSummary()
            ->willReturn('number');
        $otherRequiredStaticDefinition->isRequired()
            ->willReturn(false); // Doesn't need to _actually_ be required in the other model
        $this->staticDefinition2
            ->allowsStaticDefinition($otherRequiredStaticDefinition)
            ->willReturn(true);
        // A required static that the other model defines but this one does not
        $anotherStaticDefinition = $this->prophesize(FixedStaticDefinition::class);
        $anotherStaticDefinition->getName()
            ->willReturn('another-static');
        $anotherStaticDefinition->getStaticTypeSummary()
            ->willReturn('number');
        $anotherStaticDefinition->isRequired()
            ->willReturn(false);
        $otherBagModel = new FixedStaticBagModel($this->bagFactory->reveal(), [
            $otherRequiredStaticDefinition->reveal(),
            $anotherStaticDefinition->reveal()
        ]);

        static::assertFalse($this->bagModel->allowsOtherModel($otherBagModel));
    }

    public function testAllowsOtherModelReturnsFalseWhenTheOtherModelContainsAnOptionalStaticNotAllowedByItsEquivalentInThisOne()
    {
        $otherOptionalStaticDefinition = $this->prophesize(FixedStaticDefinition::class);
        $otherOptionalStaticDefinition->getName()
            ->willReturn('optional-static');
        $otherOptionalStaticDefinition->getStaticTypeSummary()
            ->willReturn('number');
        $otherOptionalStaticDefinition->isRequired()
            ->willReturn(false);
        $this->staticDefinition1
            ->allowsStaticDefinition($otherOptionalStaticDefinition)
            ->willReturn(false); // Don't allow `optional-static` to be accepted by this one (wrong type)
        $otherRequiredStaticDefinition = $this->prophesize(FixedStaticDefinition::class);
        $otherRequiredStaticDefinition->getName()
            ->willReturn('required-static');
        $otherRequiredStaticDefinition->getStaticTypeSummary()
            ->willReturn('number');
        $otherRequiredStaticDefinition->isRequired()
            ->willReturn(false); // Doesn't need to _actually_ be required in the other model
        $this->staticDefinition2
            ->allowsStaticDefinition($otherRequiredStaticDefinition)
            ->willReturn(true);
        $otherBagModel = new FixedStaticBagModel($this->bagFactory->reveal(), [
            $otherOptionalStaticDefinition->reveal(),
            $otherRequiredStaticDefinition->reveal()
        ]);

        static::assertFalse($this->bagModel->allowsOtherModel($otherBagModel));
    }

    public function testAllowsOtherModelReturnsFalseWhenTheOtherModelContainsARequiredStaticNotAllowedByItsEquivalentInThisOne()
    {
        $otherOptionalStaticDefinition = $this->prophesize(FixedStaticDefinition::class);
        $otherOptionalStaticDefinition->getName()
            ->willReturn('optional-static');
        $otherOptionalStaticDefinition->getStaticTypeSummary()
            ->willReturn('text');
        $otherOptionalStaticDefinition->isRequired()
            ->willReturn(true);
        $this->staticDefinition1
            ->allowsStaticDefinition($otherOptionalStaticDefinition)
            ->willReturn(true);
        $otherRequiredStaticDefinition = $this->prophesize(FixedStaticDefinition::class);
        $otherRequiredStaticDefinition->getName()
            ->willReturn('required-static');
        $otherRequiredStaticDefinition->getStaticTypeSummary()
            ->willReturn('text');
        $otherRequiredStaticDefinition->isRequired()
            ->willReturn(false); // Doesn't need to _actually_ be required in the other model
        $this->staticDefinition2
            ->allowsStaticDefinition($otherRequiredStaticDefinition)
            ->willReturn(false); // Don't allow `required-static` to be accepted by this one (wrong type)
        $otherBagModel = new FixedStaticBagModel($this->bagFactory->reveal(), [
            $otherOptionalStaticDefinition->reveal(),
            $otherRequiredStaticDefinition->reveal()
        ]);

        static::assertFalse($this->bagModel->allowsOtherModel($otherBagModel));
    }

    public function testGetStaticDefinitionByNameReturnsTheDefinitionWhenItExists()
    {
        $dynamicActNodeAdopter = $this->prophesize(DynamicActNodeAdopterInterface::class);

        static::assertSame(
            $this->staticDefinition2->reveal(),
            $this->bagModel->getStaticDefinitionByName('required-static', $dynamicActNodeAdopter->reveal())
        );
    }

    public function testGetStaticDefinitionByNameThrowsExceptionWhenItDoesNotExist()
    {
        $dynamicActNodeAdopter = $this->prophesize(DynamicActNodeAdopterInterface::class);

        $this->setExpectedException(
            LogicException::class,
            'Bag model does not define static "my-undefined-static"'
        );

        $this->bagModel->getStaticDefinitionByName('my-undefined-static', $dynamicActNodeAdopter->reveal());
    }

    public function testGetStaticDefinitionNamesReturnsAllNames()
    {
        static::assertEquals(
            ['optional-static', 'required-static'],
            $this->bagModel->getStaticDefinitionNames()
        );
    }

    public function testGetStaticDefinitionsReturnsAllDefinitionsIndexedByName()
    {
        static::assertSame(
            [
                'optional-static' => $this->staticDefinition1->reveal(),
                'required-static' => $this->staticDefinition2->reveal()
            ],
            $this->bagModel->getStaticDefinitions()
        );
    }

    public function testGetSummaryReturnsTheCorrectString()
    {
        static::assertSame('{optional-static: text, required-static: number}', $this->bagModel->getSummary());
    }
}
