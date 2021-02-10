<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Environment\Config\Act;

use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Config\Act\EnvironmentNodePromoter;
use Combyna\Component\Environment\Config\Act\LibraryNode;
use Combyna\Component\Environment\Config\Act\LibraryNodePromoter;
use Combyna\Component\Environment\EnvironmentFactoryInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class EnvironmentNodePromoterTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentNodePromoterTest extends TestCase
{
    /**
     * @var ObjectProphecy|EnvironmentInterface
     */
    private $environment;

    /**
     * @var ObjectProphecy|EnvironmentFactoryInterface
     */
    private $environmentFactory;

    /**
     * @var ObjectProphecy|EnvironmentNode
     */
    private $environmentNode;

    /**
     * @var ObjectProphecy|LibraryNode
     */
    private $libraryNode1;

    /**
     * @var ObjectProphecy|LibraryNode
     */
    private $libraryNode2;

    /**
     * @var ObjectProphecy|LibraryNode
     */
    private $libraryNode3;

    /**
     * @var ObjectProphecy|LibraryNodePromoter
     */
    private $libraryNodePromoter;

    /**
     * @var EnvironmentNodePromoter
     */
    private $promoter;

    public function setUp()
    {
        $this->environment = $this->prophesize(EnvironmentInterface::class);
        $this->environmentFactory = $this->prophesize(EnvironmentFactoryInterface::class);
        $this->environmentNode = $this->prophesize(EnvironmentNode::class);
        $this->libraryNode1 = $this->prophesize(LibraryNode::class);
        $this->libraryNode2 = $this->prophesize(LibraryNode::class);
        $this->libraryNode3 = $this->prophesize(LibraryNode::class);
        $this->libraryNodePromoter = $this->prophesize(LibraryNodePromoter::class);

        $this->environmentFactory->create()
            ->willReturn($this->environment);
        $this->environmentNode->getLibraries()
            ->willReturn([
                $this->libraryNode1->reveal(),
                $this->libraryNode2->reveal(),
                $this->libraryNode3->reveal()
            ]);
        $this->libraryNode1->getName()
            ->willReturn('my_first_lib');
        $this->libraryNode1->referencesLibrary('my_second_lib')
            ->willReturn(true);
        $this->libraryNode1->referencesLibrary(LibraryInterface::CORE)
            ->willReturn(false);
        $this->libraryNode2->getName()
            ->willReturn('my_second_lib');
        $this->libraryNode2->referencesLibrary('my_first_lib')
            ->willReturn(false);
        $this->libraryNode2->referencesLibrary(LibraryInterface::CORE)
            ->willReturn(false);
        $this->libraryNode3->getName()
            ->willReturn(LibraryInterface::CORE);
        $this->libraryNode3->referencesLibrary('my_first_lib')
            ->willReturn(false);
        $this->libraryNode3->referencesLibrary('my_second_lib')
            ->willReturn(false);

        $this->libraryNodePromoter->promoteLibrary(Argument::type(LibraryNode::class), $this->environment)
            ->will($this->noBind(function (array $args) {
                /** @var LibraryNode $libraryNode */
                /** @var EnvironmentInterface $environment */
                list($libraryNode, $environment) = $args;

                $library = $this->prophesize(LibraryInterface::class);

                return $library->reveal();
            }));

        $this->promoter = new EnvironmentNodePromoter(
            $this->environmentFactory->reveal(),
            $this->libraryNodePromoter->reveal()
        );
    }

    public function testPromoteEnvironmentShouldReturnTheCreatedEnvironment()
    {
        $environment = $this->promoter->promoteEnvironment($this->environmentNode->reveal());

        $this->assert($environment)->exactlyEquals($this->environment->reveal());
    }

    public function testPromoteEnvironmentShouldSortTheLibrariesByDependenciesBeforePromoting()
    {
        $log = [];
        $this->environment->installLibrary(Argument::type(LibraryInterface::class))
            ->will(function (array $args) use (&$log) {
                /** @var LibraryInterface $library */
                list($library) = $args;

                $log[] = 'install library ' . $library->getName();
            });
        $this->libraryNodePromoter->promoteLibrary(Argument::type(LibraryNode::class), $this->environment)
            ->will($this->noBind(function (array $args) use (&$log) {
                /** @var LibraryNode $libraryNode */
                /** @var EnvironmentInterface $environment */
                list($libraryNode, $environment) = $args;
                $log[] = 'promote library ' . $libraryNode->getName();

                /** @var ObjectProphecy|LibraryInterface $library */
                $library = $this->prophesize(LibraryInterface::class);
                $library->getName()->willReturn($libraryNode->getName());

                return $library->reveal();
            }));

        $environment = $this->promoter->promoteEnvironment($this->environmentNode->reveal());

        $this->assert($log)->equals([
            // Core library should be promoted and installed first, despite no other
            // libraries depending on it
            'promote library core',
            'install library core',
            // "First" lib depends on "second" lib, so "second" should be promoted
            // and installed first
            'promote library my_second_lib',
            'install library my_second_lib',
            'promote library my_first_lib',
            'install library my_first_lib',
        ]);
    }
}
