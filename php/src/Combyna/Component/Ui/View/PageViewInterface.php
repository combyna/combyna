<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
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
use Combyna\Component\Ui\State\View\PageViewStateInterface;

/**
 * Interface PageViewInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface PageViewInterface extends ViewInterface
{
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
}
