<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Evaluation;

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;

/**
 * Interface UiEvaluationContextTreeFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface UiEvaluationContextTreeFactoryInterface
{
    /**
     * Creates a ViewEvaluationContext along with all its ancestors from the given page view state
     *
     * @param PageViewStateInterface $pageViewState
     * @param ProgramInterface $program
     * @param EnvironmentInterface $environment
     * @return ViewEvaluationContextInterface
     */
    public function createPageViewEvaluationContextTree(
        PageViewStateInterface $pageViewState,
        ProgramInterface $program,
        EnvironmentInterface $environment
    );

    /**
     * Creates a WidgetEvaluationContext along with all its ancestors from the given path to a widget state
     *
     * @param WidgetStatePathInterface $widgetStatePath
     * @param ProgramInterface $program
     * @param EnvironmentInterface $environment
     * @return WidgetEvaluationContextInterface
     */
    public function createWidgetEvaluationContextTree(
        WidgetStatePathInterface $widgetStatePath,
        ProgramInterface $program,
        EnvironmentInterface $environment
    );
}
