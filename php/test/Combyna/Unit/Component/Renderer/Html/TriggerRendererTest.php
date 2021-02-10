<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Renderer\Html;

use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Renderer\Html\TriggerRenderer;
use Combyna\Component\Trigger\TriggerCollectionInterface;
use Combyna\Component\Trigger\TriggerInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\TextWidgetInterface;
use Combyna\Harness\TestCase;
use LogicException;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class TriggerRendererTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TriggerRendererTest extends TestCase
{
    /**
     * @var ObjectProphecy|ProgramInterface
     */
    private $program;

    /**
     * @var TriggerRenderer
     */
    private $renderer;

    /**
     * @var ObjectProphecy|WidgetStatePathInterface
     */
    private $widgetStatePath;

    public function setUp()
    {
        $this->program = $this->prophesize(ProgramInterface::class);
        $this->widgetStatePath = $this->prophesize(WidgetStatePathInterface::class);

        $this->widgetStatePath->getWidgetPath()
            ->willReturn(['path', 'to', 'my-widget']);

        $this->renderer = new TriggerRenderer();
    }

    public function testRenderTriggersReturnsCorrectArrayStructure()
    {
        $widget = $this->prophesize(DefinedWidgetInterface::class);
        $this->program->getWidgetByPath(['path', 'to', 'my-widget'])
            ->willReturn($widget->reveal());
        $trigger1 = $this->prophesize(TriggerInterface::class);
        $trigger1->getEventLibraryName()
            ->willReturn('first_lib');
        $trigger1->getEventName()
            ->willReturn('first_event');
        $trigger2 = $this->prophesize(TriggerInterface::class);
        $trigger2->getEventLibraryName()
            ->willReturn('second_lib');
        $trigger2->getEventName()
            ->willReturn('second_event');
        $triggerCollection = $this->prophesize(TriggerCollectionInterface::class);
        $triggerCollection->getAll()
            ->willReturn([
                $trigger1->reveal(),
                $trigger2->reveal()
            ]);
        $widget->getTriggers()
            ->willReturn($triggerCollection->reveal());

        self::assertEquals(
            [
                ['library' => 'first_lib', 'event' => 'first_event'],
                ['library' => 'second_lib', 'event' => 'second_event'],
            ],
            $this->renderer->renderTriggers($this->widgetStatePath->reveal(), $this->program->reveal())
        );
    }

    public function testRenderTriggersThrowsWhenAnInvalidTypeOfWidgetStateIsGiven()
    {
        $widget = $this->prophesize(TextWidgetInterface::class);
        $this->program->getWidgetByPath(['path', 'to', 'my-widget'])
            ->willReturn($widget->reveal());

        $this->setExpectedException(
            LogicException::class,
            sprintf(
                'Expected a %s, got a %s',
                DefinedWidgetInterface::class,
                get_class($widget->reveal())
            )
        );

        $this->renderer->renderTriggers(
            $this->widgetStatePath->reveal(),
            $this->program->reveal()
        );
    }
}
