<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Framework;

use Combyna\Component\App\AppInterface;
use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\App\Config\Act\AppNodePromoter;
use Combyna\Component\App\Config\Loader\AppLoaderInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Config\Loader\EnvironmentLoaderInterface;
use Combyna\Component\Environment\EnvironmentFactory;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Framework\Context\ModeContext;
use Combyna\Component\Framework\EventDispatcher\Event\EnvironmentLoadedEvent;
use Combyna\Component\Framework\FrameworkEvents;
use Combyna\Component\Framework\Mode\DevelopmentMode;
use Combyna\Component\Framework\Mode\ProductionMode;
use Combyna\Component\Plugin\LibraryConfigCollection;
use Combyna\Component\Program\Validation\Validator\NodeValidatorInterface;
use Combyna\Component\Validator\Context\RootValidationContextInterface;
use Combyna\Harness\TestCase;
use LogicException;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CombynaTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CombynaTest extends TestCase
{
    /**
     * @var ObjectProphecy|AppInterface
     */
    private $app;

    /**
     * @var ObjectProphecy|AppLoaderInterface
     */
    private $appLoader;

    /**
     * @var ObjectProphecy|AppNode
     */
    private $appNode;

    /**
     * @var ObjectProphecy|AppNodePromoter
     */
    private $appNodePromoter;

    /**
     * @var ObjectProphecy|NodeValidatorInterface
     */
    private $appValidator;

    /**
     * @var Combyna
     */
    private $combyna;

    /**
     * @var ObjectProphecy|EnvironmentFactory
     */
    private $environmentFactory;

    /**
     * @var EnvironmentLoaderInterface
     */
    private $environmentLoader;

    /**
     * @var ObjectProphecy|EnvironmentNode
     */
    private $environmentNode;

    /**
     * @var ObjectProphecy|EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ObjectProphecy|LibraryConfigCollection
     */
    private $libraryConfigCollection;

    /**
     * @var ObjectProphecy|ModeContext
     */
    private $modeContext;

    /**
     * @var ObjectProphecy|RootValidationContextInterface
     */
    private $rootValidationContext;

    public function setUp()
    {
        $this->app = $this->prophesize(AppInterface::class);
        $this->appLoader = $this->prophesize(AppLoaderInterface::class);
        $this->appNode = $this->prophesize(AppNode::class);
        $this->appNodePromoter = $this->prophesize(AppNodePromoter::class);
        $this->appValidator = $this->prophesize(NodeValidatorInterface::class);
        $this->environmentFactory = $this->prophesize(EnvironmentFactory::class);
        $this->environmentLoader = $this->prophesize(EnvironmentLoaderInterface::class);
        $this->environmentNode = $this->prophesize(EnvironmentNode::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->libraryConfigCollection = $this->prophesize(LibraryConfigCollection::class);
        $this->modeContext = $this->prophesize(ModeContext::class);
        $this->rootValidationContext = $this->prophesize(RootValidationContextInterface::class);

        $this->appLoader->loadApp($this->environmentNode, ['my_app' => true])
            ->willReturn($this->appNode);
        $this->appNodePromoter->promoteApp($this->appNode, $this->environmentNode)
            ->willReturn($this->app);
        $this->appValidator->validate($this->appNode, $this->appNode)
            ->willReturn($this->rootValidationContext);
        $this->environmentLoader
            ->loadEnvironment([
                'libraries' => [
                    ['lib' => 1],
                    ['lib' => 2]
                ]
            ])
            ->willReturn($this->environmentNode);
        $this->libraryConfigCollection->getLibraryConfigs()
            ->willReturn([
                ['lib' => 1],
                ['lib' => 2]
            ]);
        $this->modeContext->getMode()
            ->willReturn(new DevelopmentMode());
        $this->modeContext->useProductionMode()
            ->willReturn();

        $this->combyna = new Combyna(
            $this->eventDispatcher->reveal(),
            $this->environmentFactory->reveal(),
            $this->environmentLoader->reveal(),
            $this->appLoader->reveal(),
            $this->appValidator->reveal(),
            $this->appNodePromoter->reveal(),
            $this->libraryConfigCollection->reveal(),
            $this->modeContext->reveal()
        );
    }

    public function testCreateAppShouldReturnACorrectlyLoadedThenPromotedAppWhenGivenAnExistingEnvironment()
    {
        $app = $this->combyna->createApp(['my_app' => true], $this->environmentNode->reveal());

        self::assertSame($this->app->reveal(), $app);
    }

    public function testCreateAppShouldReturnACorrectlyLoadedThenPromotedAppWhenNotGivenAnExistingEnvironment()
    {
        $app = $this->combyna->createApp(['my_app' => true]);

        self::assertSame($this->app->reveal(), $app);
    }

    public function testCreateAppShouldValidateTheAppWhenInDevelopmentMode()
    {
        $this->combyna->createApp(['my_app' => true], $this->environmentNode->reveal());

        $this->appValidator->validate($this->appNode, $this->appNode)
            ->shouldHaveBeenCalled();
        $this->rootValidationContext->throwIfViolated()
            ->shouldHaveBeenCalled();
    }

    public function testCreateAppShouldNotValidateTheAppWhenInProductionMode()
    {
        $this->modeContext->getMode()->willReturn(new ProductionMode());

        $this->combyna->createApp(['my_app' => true], $this->environmentNode->reveal());

        $this->appValidator->validate($this->appNode, $this->appNode)
            ->shouldNotHaveBeenCalled();
        $this->rootValidationContext->throwIfViolated()
            ->shouldNotHaveBeenCalled();
    }

    public function testCreateEnvironmentShouldReturnACorrectlyLoadedEnvironmentNode()
    {
        $this->environmentLoader
            ->loadEnvironment([
                'libraries' => [
                    ['lib' => 1],
                    ['lib' => 2],
                    ['lib' => 3] // Add the third library passed as an arg
                ]
            ])
            ->willReturn($this->environmentNode);

        $environmentNode = $this->combyna->createEnvironment([
            'libraries' => [
                ['lib' => 3] // Add a third library
            ]
        ]);

        self::assertSame($this->environmentNode->reveal(), $environmentNode);
    }

    public function testCreateEnvironmentShouldDispatchAnEventWithTheLoadedEnvironmentNode()
    {
        $this->eventDispatcher->dispatch(FrameworkEvents::ENVIRONMENT_LOADED, Argument::any())
            ->will($this->noBind(function (array $args) {
                /** @var EnvironmentLoadedEvent $event */
                list(, $event) = $args;

                $this->assert($event)->isAnInstanceOf(EnvironmentLoadedEvent::class);
                $this->assert($event->getEnvironmentNode())->exactlyEquals($this->environmentNode->reveal());
            }));

        $this->combyna->createEnvironment();
    }

    public function testUseProductionModeShouldAskTheModeContextToUseProduction()
    {
        $this->combyna->useProductionMode();

        $this->modeContext->useProductionMode()->shouldHaveBeenCalled();
    }

    public function testUseProductionModeShouldThrowWhenAnAppHasAlreadyBeenCreated()
    {
        $this->combyna->createApp(['my_app' => true], $this->environmentNode->reveal());

        $this->setExpectedException(
            LogicException::class,
            'Unable to switch to production mode, as an app has already been created'
        );

        $this->combyna->useProductionMode();
    }
}
