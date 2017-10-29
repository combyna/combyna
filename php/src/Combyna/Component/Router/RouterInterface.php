<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router;

use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Router\State\RouterStateInterface;
use Combyna\Component\Ui\View\PageViewCollectionInterface;

/**
 * Interface RouterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RouterInterface
{
    /**
     * Creates a new RouterState object, with its route and arguments set to those defined
     * as the initial route and arguments for this Router
     *
     * @param EvaluationContextInterface $evaluationContext
     * @return RouterStateInterface
     */
    public function createInitialState(EvaluationContextInterface $evaluationContext);

    /**
     * Navigates the app to a new location, using the specified route and its arguments
     *
     * @param \Combyna\Component\Program\State\ProgramStateInterface $programState
     * @param ProgramInterface $program
     * @param string $libraryName
     * @param string $routeName
     * @param StaticBagInterface $routeArgumentBag
     * @param PageViewCollectionInterface $pageViewCollection
     * @param EvaluationContextInterface $evaluationContext
     * @return ProgramStateInterface
     */
    public function navigateTo(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        $libraryName,
        $routeName,
        StaticBagInterface $routeArgumentBag,
        PageViewCollectionInterface $pageViewCollection,
        EvaluationContextInterface $evaluationContext
    );
}
