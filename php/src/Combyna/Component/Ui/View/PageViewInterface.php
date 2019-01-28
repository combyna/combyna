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
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Ui\Evaluation\RootViewEvaluationContext;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;

/**
 * Interface PageViewInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface PageViewInterface extends ViewInterface
{
    /**
     * Creates a RootViewEvaluationContext
     *
     * @param EvaluationContextInterface $parentContext
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param PageViewStateInterface|null $pageViewState
     * @return RootViewEvaluationContext
     */
    public function createEvaluationContext(
        EvaluationContextInterface $parentContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        PageViewStateInterface $pageViewState = null
    );

    /**
     * Creates an initial state for the page view
     *
     * @param EvaluationContextInterface $rootEvaluationContext
     * @return PageViewStateInterface
     */
    public function createInitialState(EvaluationContextInterface $rootEvaluationContext);

    /**
     * Performs the actual internal handling of a dispatched signal
     *
     * @param PageViewStateInterface $pageViewState
     * @param SignalInterface $signal
     * @param ProgramInterface $program
     * @param EnvironmentInterface $environment
     * @return PageViewStateInterface
     */
    public function handleSignal(
        PageViewStateInterface $pageViewState,
        SignalInterface $signal,
        ProgramInterface $program,
        EnvironmentInterface $environment
    );

    /**
     * Re-evaluates the state for the widget, using the old state as a base.
     * If the newly evaluated state is the same as the old one,
     * the original state object will be returned
     *
     * @param PageViewStateInterface $oldState
     * @param EvaluationContextInterface $evaluationContext
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @return PageViewStateInterface
     */
    public function reevaluateState(
        PageViewStateInterface $oldState,
        EvaluationContextInterface $evaluationContext,
        UiEvaluationContextFactoryInterface $evaluationContextFactory
    );
}
