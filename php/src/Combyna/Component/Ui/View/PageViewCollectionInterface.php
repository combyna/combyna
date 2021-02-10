<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\View;

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Router\State\RouterStateInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;

/**
 * Interface PageViewCollectionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface PageViewCollectionInterface extends ViewCollectionInterface
{
    /**
     * Creates an initial state for a page view
     *
     * @param RouterStateInterface $routerState
     * @param EvaluationContextInterface $evaluationContext
     * @return PageViewStateInterface
     */
    public function createInitialState(
        RouterStateInterface $routerState,
        EvaluationContextInterface $evaluationContext
    );

    /**
     * {@inheritdoc}
     *
     * @return PageViewInterface
     */
    public function getView($viewName);

    /**
     * Handles a signal for the active page
     *
     * @param ProgramStateInterface $programState
     * @param SignalInterface $signal
     * @param ProgramInterface $program
     * @param EnvironmentInterface $environment
     * @return ProgramStateInterface
     */
    public function handleSignal(
        ProgramStateInterface $programState,
        SignalInterface $signal,
        ProgramInterface $program,
        EnvironmentInterface $environment
    );

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function hasView($name);
}
