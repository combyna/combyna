<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Event\EventDispatcherInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Router\RouteInterface;
use Combyna\Component\Router\RouterInterface;
use Combyna\Component\Signal\DispatcherInterface;
use Combyna\Component\Signal\SignalDefinitionRepositoryInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextTreeFactoryInterface;
use Combyna\Component\Ui\View\OverlayViewCollectionInterface;
use Combyna\Component\Ui\View\PageViewCollectionInterface;

/**
 * Class App
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppFactory implements AppFactoryInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var DispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var UiEvaluationContextTreeFactoryInterface
     */
    private $uiEvaluationContextTreeFactory;

    /**
     * @param BagFactoryInterface $bagFactory
     * @param DispatcherInterface $dispatcher
     * @param EventDispatcherInterface $eventDispatcher
     * @param ExpressionFactoryInterface $expressionFactory
     * @param UiEvaluationContextTreeFactoryInterface $uiEvaluationContextTreeFactory
     */
    public function __construct(
        BagFactoryInterface $bagFactory,
        DispatcherInterface $dispatcher,
        EventDispatcherInterface $eventDispatcher,
        ExpressionFactoryInterface $expressionFactory,
        UiEvaluationContextTreeFactoryInterface $uiEvaluationContextTreeFactory
    ) {
        $this->bagFactory = $bagFactory;
        $this->dispatcher = $dispatcher;
        $this->eventDispatcher = $eventDispatcher;
        $this->expressionFactory = $expressionFactory;
        $this->uiEvaluationContextTreeFactory = $uiEvaluationContextTreeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(
        RouterInterface $router,
        SignalDefinitionRepositoryInterface $signalDefinitionRepository,
        PageViewCollectionInterface $pageViewCollection,
        OverlayViewCollectionInterface $overlayViewCollection,
        EnvironmentInterface $environment,
        ProgramInterface $program
    ) {
        return new App(
            $this->bagFactory,
            $this->expressionFactory,
            $this->dispatcher,
            $this->eventDispatcher,
            $router,
            $signalDefinitionRepository,
            $pageViewCollection,
            $overlayViewCollection,
            $this->uiEvaluationContextTreeFactory,
            $environment,
            $program
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createHome(RouteInterface $route, ExpressionBagInterface $attributeExpressionBag)
    {
        return new Home($route, $attributeExpressionBag);
    }
}
