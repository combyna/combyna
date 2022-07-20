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

use Combyna\Component\Renderer\Html\HtmlNodeInterface;
use Combyna\Component\Renderer\Html\RenderedWidget;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class RenderedWidgetTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RenderedWidgetTest extends TestCase
{
    /**
     * @var RenderedWidget
     */
    private $renderedWidget;

    /**
     * @var ObjectProphecy|HtmlNodeInterface
     */
    private $rootNode;

    /**
     * @var ObjectProphecy|WidgetStateInterface
     */
    private $widgetState;

    public function setUp()
    {
        $this->rootNode = $this->prophesize(HtmlNodeInterface::class);
        $this->widgetState = $this->prophesize(WidgetStateInterface::class);

        $this->rootNode->toArray()->willReturn([
            'type' => 'element',
            'tag' => 'span',
            'path' => ['my-view', 'root', 'stuff']
        ]);
        $this->rootNode->toHtml()->willReturn('<div>My root element</div>');

        $this->widgetState->getWidgetDefinitionLibraryName()->willReturn('my-library');
        $this->widgetState->getWidgetDefinitionName()->willReturn('my-widget');

        $this->renderedWidget = new RenderedWidget(
            $this->widgetState->reveal(),
            $this->rootNode->reveal()
        );
    }

    public function testToArrayReturnsTheCorrectAssociativeArrayStructure()
    {
        static::assertEquals([
            'type' => 'widget',
            'library' => 'my-library',
            'widget' => 'my-widget',
            'root' => [
                'type' => 'element',
                'tag' => 'span',
                'path' => ['my-view', 'root', 'stuff']
            ]
        ], $this->renderedWidget->toArray());
    }

    public function testToHtmlReturnsTheRenderedHtmlOfTheRootElement()
    {
        static::assertSame('<div>My root element</div>', $this->renderedWidget->toHtml());
    }
}
