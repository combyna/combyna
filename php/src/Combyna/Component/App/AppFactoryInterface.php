<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Router\RouteInterface;
use Combyna\Component\Router\RouterInterface;
use Combyna\Component\Signal\SignalDefinitionRepositoryInterface;
use Combyna\Component\Ui\View\OverlayViewCollectionInterface;
use Combyna\Component\Ui\View\PageViewCollectionInterface;

/**
 * Interface AppFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AppFactoryInterface
{
    /**
     * Creates a new app
     *
     * @param RouterInterface $router
     * @param SignalDefinitionRepositoryInterface $signalDefinitionRepository
     * @param PageViewCollectionInterface $pageViewCollection
     * @param OverlayViewCollectionInterface $overlayViewCollection
     * @param EnvironmentInterface $environment
     * @param ProgramInterface $program
     * @return AppInterface
     */
    public function create(
        RouterInterface $router,
        SignalDefinitionRepositoryInterface $signalDefinitionRepository,
        PageViewCollectionInterface $pageViewCollection,
        OverlayViewCollectionInterface $overlayViewCollection,
        EnvironmentInterface $environment,
        ProgramInterface $program
    );

    /**
     * Creates a new home
     *
     * @param RouteInterface $route
     * @param ExpressionBagInterface $attributeExpressionBag
     * @return HomeInterface
     */
    public function createHome(RouteInterface $route, ExpressionBagInterface $attributeExpressionBag);
}
