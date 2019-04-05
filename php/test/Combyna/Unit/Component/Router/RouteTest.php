<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Router;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Router\Route;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class RouteTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteTest extends TestCase
{
    /**
     * @var ObjectProphecy|FixedStaticBagModelInterface
     */
    private $parameterBagModel;

    /**
     * @var Route
     */
    private $route;

    public function setUp()
    {
        $this->parameterBagModel = $this->prophesize(FixedStaticBagModelInterface::class);

        $this->route = new Route(
            'my_route',
            '/my/url/pattern/with/{param1}/and/{param2}',
            $this->parameterBagModel->reveal(),
            'my_page_view'
        );
    }

    public function testGenerateUrlReturnsCorrectUrlWhenUrlHasPlaceholders()
    {
        $argumentBag = $this->prophesize(StaticBagInterface::class);
        $argumentBag->toNativeArray()->willReturn([
            'param1' => 'my_first_segment',
            'param2' => 'my_second_segment'
        ]);
        $this->parameterBagModel->assertValidStaticBag($argumentBag)->shouldBeCalled();

        $this->assert($this->route->generateUrl($argumentBag->reveal()))
            ->exactlyEquals('/my/url/pattern/with/my_first_segment/and/my_second_segment');
    }

    public function testGenerateUrlReturnsCorrectUrlWhenUrlHasNoParameterPlaceholders()
    {
        $argumentBag = $this->prophesize(StaticBagInterface::class);
        $argumentBag->toNativeArray()->willReturn([]);
        $this->parameterBagModel->assertValidStaticBag($argumentBag)->shouldBeCalled();
        $this->route = new Route(
            'my_route',
            '/my/url/pattern/with/no/parameters',
            $this->parameterBagModel->reveal(),
            'my_page_view'
        );

        $this->assert($this->route->generateUrl($argumentBag->reveal()))
            ->exactlyEquals('/my/url/pattern/with/no/parameters');
    }

    public function testGetUrlPatternReturnsThePattern()
    {
        $this->assert($this->route->getUrlPattern())
            ->exactlyEquals('/my/url/pattern/with/{param1}/and/{param2}');
    }
}
