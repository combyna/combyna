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

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Event\EventDefinitionReferenceCollectionInterface;
use Combyna\Component\Event\EventFactoryInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\Widget\PrimitiveWidgetDefinition;
use Combyna\Harness\TestCase;
use LogicException;
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
     * @var ObjectProphecy|BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var ObjectProphecy|StaticInterface
     */
    private $coercedValueStatic;

    /**
     * @var PrimitiveWidgetDefinition
     */
    private $definition;

    /**
     * @var ObjectProphecy|ViewEvaluationContextInterface
     */
    private $evaluationContext;

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
     * @var ObjectProphecy|StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

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
        $this->bagFactory = $this->prophesize(BagFactoryInterface::class);
        $this->coercedValueStatic = $this->prophesize(StaticInterface::class);
        $this->evaluationContext = $this->prophesize(ViewEvaluationContextInterface::class);
        $this->eventDefinitionReferenceCollection = $this->prophesize(
            EventDefinitionReferenceCollectionInterface::class
        );
        $this->eventFactory = $this->prophesize(EventFactoryInterface::class);
        $this->firstValueStatic = $this->prophesize(StaticInterface::class);
        $this->staticExpressionFactory = $this->prophesize(StaticExpressionFactoryInterface::class);
        $this->uiEvaluationContextFactory = $this->prophesize(UiEvaluationContextFactoryInterface::class);
        $this->uiStateFactory = $this->prophesize(UiStateFactoryInterface::class);
        $this->valueBagModel = $this->prophesize(FixedStaticBagModelInterface::class);

        $this->definition = new PrimitiveWidgetDefinition(
            $this->bagFactory->reveal(),
            $this->uiStateFactory->reveal(),
            $this->uiEvaluationContextFactory->reveal(),
            $this->eventFactory->reveal(),
            $this->eventDefinitionReferenceCollection->reveal(),
            'my_lib',
            'my_widget',
            $this->attributeBagModel->reveal(),
            $this->valueBagModel->reveal(),
            $this->staticExpressionFactory->reveal(),
            [
                'first_value' => function () {
                    return $this->firstValueStatic->reveal();
                },
                'second_value' => function () {
                    return 'a native result';
                }
            ]
        );
    }

    public function testGetWidgetValueReturnsTheStaticFromTheProvider()
    {
        $firstValueType = $this->prophesize(TypeInterface::class);
        $firstValueType->coerceNative(
            $this->firstValueStatic,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext
        )
            ->willReturn($this->firstValueStatic);
        $this->valueBagModel->getStaticType('first_value')->willReturn($firstValueType);

        static::assertSame(
            $this->firstValueStatic->reveal(),
            $this->definition->getWidgetValue(
                'first_value',
                ['path', 'to'],
                $this->evaluationContext->reveal()
            )
        );
    }

    public function testGetWidgetValueCoercesANativeFromTheProviderToAStatic()
    {
        $secondValueType = $this->prophesize(TypeInterface::class);
        $secondValueType->coerceNative(
            'a native result',
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext
        )
            ->willReturn($this->coercedValueStatic);
        $this->valueBagModel->getStaticType('second_value')->willReturn($secondValueType);

        static::assertSame(
            $this->coercedValueStatic->reveal(),
            $this->definition->getWidgetValue(
                'second_value',
                ['path', 'to'],
                $this->evaluationContext->reveal()
            )
        );
    }

    public function testGetWidgetValueThrowsExceptionWhenValueHasNoProvider()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'No provider was installed for widget value "value_with_no_provider"'
        );

        $this->definition->getWidgetValue(
            'value_with_no_provider',
            ['path', 'to'],
            $this->evaluationContext->reveal()
        );
    }
}
