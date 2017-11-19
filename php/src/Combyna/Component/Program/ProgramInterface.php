<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Program;

use Combyna\Component\Expression\Evaluation\RootEvaluationContext;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Signal\SignalDefinitionInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Ui\View\PageViewInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Interface ProgramInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ProgramInterface
{
    /**
     * Fetches a page view by its unique name
     *
     * @param string $name
     * @return PageViewInterface
     */
    public function getPageViewByName($name);

    /**
     * Fetches the root evaluation context for the app
     *
     * @return RootEvaluationContext
     */
    public function getRootEvaluationContext();

    /**
     * Fetches a signal definition with the given name from the current app or a library in the environment
     *
     * @param string $libraryName
     * @param string $signalName
     * @return SignalDefinitionInterface
     */
    public function getSignalDefinitionByName($libraryName, $signalName);

    /**
     * Fetches a widget (from any type of view) by its path
     *
     * @param string[]|int[] $names
     * @return WidgetInterface
     */
    public function getWidgetByPath(array $names);

    /**
     * Fetches a widget definition with the given name from a library in the environment
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @return WidgetDefinitionInterface
     */
    public function getWidgetDefinitionByName($libraryName, $widgetDefinitionName);

    /**
     * Performs the actual internal handling of a dispatched signal
     *
     * @param ProgramStateInterface $programState
     * @param SignalInterface $signal
     * @return ProgramStateInterface
     */
    public function handleSignal(
        ProgramStateInterface $programState,
        SignalInterface $signal
    );
}
