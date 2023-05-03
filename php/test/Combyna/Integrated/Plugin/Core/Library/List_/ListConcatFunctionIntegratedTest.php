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
 * Class ListConcatFunctionIntegratedTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ListConcatFunctionIntegratedTest extends TestCase
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

    public function testCanConcatenateTwoListsOfText()
    {
        $list1 = $this->staticExpressionFactory->createStaticListExpression(
            $this->bagFactory->createStaticList(
                [
                    $this->staticExpressionFactory->createTextExpression('first'),
                    $this->staticExpressionFactory->createTextExpression('second')
                ]
            )
        );
        $list2 = $this->staticExpressionFactory->createStaticListExpression(
            $this->bagFactory->createStaticList(
                [
                    $this->staticExpressionFactory->createTextExpression('third'),
                    $this->staticExpressionFactory->createTextExpression('fourth')
                ]
            )
        );
        $listOfListsStatic = $this->staticExpressionFactory->createStaticListExpression(
            $this->bagFactory->createStaticList([$list1, $list2])
        );

        $resultStaticList = $this->listFunctions->concat($listOfListsStatic);
        $resultArray = $resultStaticList->toNative();

        static::assertCount(4, $resultArray);
        static::assertSame('[first,second,third,fourth]', $resultStaticList->getSummary());
    }
}
