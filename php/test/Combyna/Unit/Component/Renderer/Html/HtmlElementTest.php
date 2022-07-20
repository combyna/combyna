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

use Combyna\Component\Renderer\Html\HtmlElement;
use Combyna\Component\Renderer\Html\HtmlNodeInterface;
use Combyna\Harness\TestCase;
use LogicException;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class HtmlElementTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class HtmlElementTest extends TestCase
{
    /**
     * @var ObjectProphecy|HtmlNodeInterface
     */
    private $childNode1;

    /**
     * @var ObjectProphecy|HtmlNodeInterface
     */
    private $childNode2;

    /**
     * @var HtmlElement
     */
    private $htmlElement;

    public function setUp()
    {
        $this->childNode1 = $this->prophesize(HtmlNodeInterface::class);
        $this->childNode2 = $this->prophesize(HtmlNodeInterface::class);

        $this->childNode1->toArray()->willReturn([
            'type' => 'first-child-type',
            'tag' => 'first-child-tag'
        ]);
        $this->childNode1->toHtml()->willReturn('<first-child-tag></first-child-tag>');
        $this->childNode2->toArray()->willReturn([
            'type' => 'second-child-type',
            'tag' => 'second-child-tag'
        ]);
        $this->childNode2->toHtml()->willReturn('<second-child-tag is-too></second-child-tag>');

        $this->htmlElement = new HtmlElement(
            'section',
            ['my-view', 'root', 'my-child'],
            [
                'first-attr' => 'first value',
                'second-attr' => 'second value'
            ],
            [$this->childNode1->reveal(), $this->childNode2->reveal()]
        );
    }

    public function testToArrayReturnsTheCorrectAssociativeArrayStructure()
    {
        static::assertEquals([
            'type' => 'element',
            'tag' => 'section',
            'path' => ['my-view', 'root', 'my-child'],
            'attributes' => [
                'first-attr' => 'first value',
                'second-attr' => 'second value'
            ],
            'children' => [
                [
                    'type' => 'first-child-type',
                    'tag' => 'first-child-tag'
                ],
                [
                    'type' => 'second-child-type',
                    'tag' => 'second-child-tag'
                ]
            ]
        ], $this->htmlElement->toArray());
    }

    public function testToHtmlReturnsTheCorrectHtml()
    {
        $expectedHtml =
            '<section first-attr="first value" second-attr="second value">' .
            '<first-child-tag></first-child-tag>' .
            '<second-child-tag is-too></second-child-tag>' .
            '</section>';

        static::assertSame($expectedHtml, $this->htmlElement->toHtml());
    }

    /**
     * @dataProvider selfClosingElementTagProvider
     * @param string $tagName
     */
    public function testToHtmlThrowsWhenSelfClosingElementsHaveChildren($tagName)
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('<' . $tagName . '> elements should have no children');

        $this->htmlElement = new HtmlElement(
            $tagName,
            ['my-view', 'root', 'my-child'],
            [
                'my-attr' => 'my value'
            ],
            [$this->childNode1->reveal()]
        );

        $this->htmlElement->toHtml();
    }

    /**
     * @return array
     */
    public function selfClosingElementTagProvider()
    {
        return [
            ['img'],
            ['input']
        ];
    }

    /**
     * @dataProvider fieldElementTagProvider
     * @param string $tagName
     * @param string $expectedHtml
     */
    public function testToHtmlAddsNameAttributeToFieldElements($tagName, $expectedHtml)
    {
        $this->htmlElement = new HtmlElement(
            $tagName,
            ['my-view', 'root', 'my-child'],
            [
                'my-attr' => 'my value'
            ]
        );

        static::assertSame($expectedHtml, $this->htmlElement->toHtml());
    }

    /**
     * @return array
     */
    public function fieldElementTagProvider()
    {
        return [
            [
                'button',
                '<button name="combyna-widget-my-view-root-my-child" my-attr="my value"></button>'
            ],
            [
                'input',
                '<input name="combyna-widget-my-view-root-my-child" my-attr="my value">'
            ],
            [
                'textarea',
                '<textarea name="combyna-widget-my-view-root-my-child" my-attr="my value"></textarea>'
            ]
        ];
    }
}
