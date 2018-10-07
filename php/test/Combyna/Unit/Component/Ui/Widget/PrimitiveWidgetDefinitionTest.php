<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Ui\Widget;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Event\EventDefinitionReferenceCollectionInterface;
use Combyna\Component\Event\EventFactoryInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\Widget\PrimitiveWidgetDefinition;
use Combyna\Harness\TestCase;
use LogicException;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class PrimitiveWidgetDefinitionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PrimitiveWidgetDefinitionTest extends TestCase
{
    /**
     * @var ObjectProphecy|FixedStaticBagModelInterface
     */
    private $attributeBagModel;

    /**
     * @var PrimitiveWidgetDefinition
     */
    private $definition;

    /**
     * @var ObjectProphecy|EventDefinitionReferenceCollectionInterface
     */
    private $eventDefinitionReferenceCollection;

    /**
     * @var ObjectProphecy|EventFactoryInterface
     */
    private $eventFactory;

    /**
     * @var ObjectProphecy|StaticInterface
     */
    private $firstValueStatic;

    /**
     * @var ObjectProphecy|UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @var ObjectProphecy|UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @var ObjectProphecy|FixedStaticBagModelInterface
     */
    private $valueBagModel;

    public function setUp()
    {
        $this->attributeBagModel = $this->prophesize(FixedStaticBagModelInterface::class);
        $this->eventDefinitionReferenceCollection = $this->prophesize(
            EventDefinitionReferenceCollectionInterface::class
        );
        $this->eventFactory = $this->prophesize(EventFactoryInterface::class);
        $this->firstValueStatic = $this->prophesize(StaticInterface::class);
        $this->uiEvaluationContextFactory = $this->prophesize(UiEvaluationContextFactoryInterface::class);
        $this->uiStateFactory = $this->prophesize(UiStateFactoryInterface::class);
        $this->valueBagModel = $this->prophesize(FixedStaticBagModelInterface::class);

        $this->definition = new PrimitiveWidgetDefinition(
            $this->uiStateFactory->reveal(),
            $this->uiEvaluationContextFactory->reveal(),
            $this->eventFactory->reveal(),
            $this->eventDefinitionReferenceCollection->reveal(),
            'my_lib',
            'my_widget',
            $this->attributeBagModel->reveal(),
            $this->valueBagModel->reveal(),
            [
                'first_value' => function () {
                    return $this->firstValueStatic->reveal();
                },
                'second_value' => function () {
                    return 'an invalid result';
                }
            ]
        );
    }

    public function testGetWidgetValueReturnsTheStaticFromTheProvider()
    {
        $firstValueType = $this->prophesize(TypeInterface::class);
        $firstValueType->allowsStatic(Argument::is($this->firstValueStatic->reveal()))->willReturn(true);
        $this->valueBagModel->getStaticType('first_value')->willReturn($firstValueType);

        $this->assert($this->definition->getWidgetValue('first_value', ['path', 'to']))
            ->isTheSameAs($this->firstValueStatic->reveal());
    }

    public function testGetWidgetValueThrowsExceptionWhenProviderReturnsANonStatic()
    {
        $this->setExpectedException(
            LogicException::class,
            'Provider for value "second_value" must return a static, string returned'
        );

        $this->definition->getWidgetValue('second_value', ['path', 'to']);
    }

    public function testGetWidgetValueThrowsExceptionWhenProviderReturnsAStaticOfIncorrectType()
    {
        $firstValueType = $this->prophesize(TypeInterface::class);
        $firstValueType->allowsStatic(Argument::is($this->firstValueStatic->reveal()))->willReturn(false);
        $firstValueType->getSummary()->willReturn('The wrong type');
        $this->valueBagModel->getStaticType('first_value')->willReturn($firstValueType);

        $this->setExpectedException(
            LogicException::class,
            'Provider for value "first_value" must return a [The wrong type]'
        );

        $this->definition->getWidgetValue('first_value', ['path', 'to']);
    }
}
