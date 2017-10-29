<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Config\Act;

use Combyna\Component\App\AppFactoryInterface;
use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Config\Act\EnvironmentNodePromoter;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Program\ProgramFactoryInterface;
use Combyna\Component\Router\Config\Act\RouteNodePromoter;
use Combyna\Component\Router\RouterFactoryInterface;
use Combyna\Component\Signal\Config\Act\SignalDefinitionNodePromoter;
use Combyna\Component\Signal\SignalFactoryInterface;
use Combyna\Component\Ui\Config\Act\ViewNodePromoter;

/**
 * Class AppNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppNodePromoter
{
    /**
     * @var AppFactoryInterface
     */
    private $appFactory;

    /**
     * @var EnvironmentNodePromoter
     */
    private $environmentNodePromoter;

    /**
     * @var EvaluationContextFactoryInterface
     */
    private $evaluationContextFactory;

    /**
     * @var HomeNodePromoter
     */
    private $homeNodePromoter;

    /**
     * @var ProgramFactoryInterface
     */
    private $programFactory;

    /**
     * @var RouteNodePromoter
     */
    private $routeNodePromoter;

    /**
     * @var RouterFactoryInterface
     */
    private $routerFactory;

    /**
     * @var SignalDefinitionNodePromoter
     */
    private $signalDefinitionNodePromoter;

    /**
     * @var SignalFactoryInterface
     */
    private $signalFactory;

    /**
     * @var ViewNodePromoter
     */
    private $viewNodePromoter;

    /**
     * @param AppFactoryInterface $appFactory
     * @param ProgramFactoryInterface $programFactory
     * @param EnvironmentNodePromoter $environmentNodePromoter
     * @param ViewNodePromoter $viewNodePromoter
     * @param RouteNodePromoter $routeNodePromoter
     * @param SignalDefinitionNodePromoter $signalDefinitionNodePromoter
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param SignalFactoryInterface $signalFactory
     * @param RouterFactoryInterface $routerFactory
     * @param HomeNodePromoter $homeNodePromoter
     */
    public function __construct(
        AppFactoryInterface $appFactory,
        ProgramFactoryInterface $programFactory,
        EnvironmentNodePromoter $environmentNodePromoter,
        ViewNodePromoter $viewNodePromoter,
        RouteNodePromoter $routeNodePromoter,
        SignalDefinitionNodePromoter $signalDefinitionNodePromoter,
        EvaluationContextFactoryInterface $evaluationContextFactory,
        SignalFactoryInterface $signalFactory,
        RouterFactoryInterface $routerFactory,
        HomeNodePromoter $homeNodePromoter
    ) {
        $this->appFactory = $appFactory;
        $this->environmentNodePromoter = $environmentNodePromoter;
        $this->evaluationContextFactory = $evaluationContextFactory;
        $this->homeNodePromoter = $homeNodePromoter;
        $this->programFactory = $programFactory;
        $this->routeNodePromoter = $routeNodePromoter;
        $this->routerFactory = $routerFactory;
        $this->signalDefinitionNodePromoter = $signalDefinitionNodePromoter;
        $this->signalFactory = $signalFactory;
        $this->viewNodePromoter = $viewNodePromoter;
    }

    /**
     * Promotes an AppNode to an App
     *
     * @param AppNode $appNode
     * @param EnvironmentNode $environmentNode
     * @return AppInterface
     */
    public function promoteApp(AppNode $appNode, EnvironmentNode $environmentNode)
    {
        $environment = $this->environmentNodePromoter->promoteEnvironment($environmentNode);

        $rootEvaluationContext = $this->evaluationContextFactory->createRootContext($environment);

        $appRouteCollection = $this->routeNodePromoter->promoteCollection($appNode->getRoutes());
        $routeRepository = $this->routerFactory->createRouteRepository($environment, $appRouteCollection);

        $appSignalDefinitionCollection = $this->signalDefinitionNodePromoter->promoteCollection(
            $appNode->getSignalDefinitions(),
            LibraryInterface::APP
        );
        $signalDefinitionRepository = $this->signalFactory->createSignalDefinitionRepository(
            $environment,
            $appSignalDefinitionCollection
        );

//        $widgetDefinitionCollection = $this->widgetDefinitionNodePromoter->promoteCollection(
//            $appNode->getWidgetDefinitions()
//        );
//        $widgetDefinitionRepository = $this->widgetFactory->createWidgetDefinitionRepository(
//            $environment,
//            $widgetDefinitionCollection
//        );

        $router = $this->routerFactory->createRouter(
            $routeRepository,
            $this->homeNodePromoter->promoteHome($appNode->getHome(), $routeRepository),
            $signalDefinitionRepository
        );

        $resourceRepository = $this->programFactory->createResourceRepository(
            $environment,
            $signalDefinitionRepository
        );
        $pageViewCollection = $this->viewNodePromoter->promotePageViewCollection(
            $appNode->getPageViews(),
            $resourceRepository
        );
        $overlayViewCollection = $this->viewNodePromoter->promoteOverlayViewCollection(
            $appNode->getOverlayViews(),
            $resourceRepository
        );
        $program = $this->programFactory->createProgram(
            $environment,
            $resourceRepository,
            $pageViewCollection,
            $overlayViewCollection,
            $rootEvaluationContext
        );

        return $this->appFactory->create(
            $router,
            $signalDefinitionRepository,
            $pageViewCollection,
            $overlayViewCollection,
            $environment,
            $program
        );
    }
}
