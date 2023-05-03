<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Plugin\Core\Library\List_;

use Combyna\Component\App\AppInterface;
use Combyna\Component\Bag\BagFactory;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Expression\StaticExpressionFactory;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Harness\TestCase;
use Combyna\Plugin\Core\Library\List_\Functions;
use Combyna\Test\Ui\TestGuiWidgetProviders;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ListCountFunctionIntegratedTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ListCountFunctionIntegratedTest extends TestCase
{
    /**
     * @var BagFactory
     */
    private $bagFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Functions
     */
    private $listFunctions;

    /**
     * @var StaticExpressionFactory
     */
    private $staticExpressionFactory;

    public function setUp()
    {
        global $combynaBootstrap;
        $this->container = $combynaBootstrap->createContainer();

        $this->listFunctions = $this->container->get('combyna_core.list.functions');
        $this->bagFactory = $this->container->get('combyna.bag.factory');
        $this->staticExpressionFactory = $this->container->get('combyna.expression.static_factory');
    }

    public function testReturnsZeroForAnEmptyList()
    {
        $listStatic = $this->staticExpressionFactory->createStaticListExpression(
            $this->bagFactory->createStaticList([])
        );

        $resultStatic = $this->listFunctions->count($listStatic);

        static::assertSame('number', $resultStatic->getType());
        static::assertSame(0, $resultStatic->toNative());
    }

    public function testReturnsTwoForAListOfTwoElements()
    {
        $listStatic = $this->staticExpressionFactory->createStaticListExpression(
            $this->bagFactory->createStaticList([
                $this->staticExpressionFactory->createTextExpression('first'),
                $this->staticExpressionFactory->createTextExpression('second')
            ])
        );

        $resultStatic = $this->listFunctions->count($listStatic);

        static::assertSame('number', $resultStatic->getType());
        static::assertSame(2, $resultStatic->toNative());
    }
}
