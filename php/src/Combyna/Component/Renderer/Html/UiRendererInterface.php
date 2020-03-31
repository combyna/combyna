<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Renderer\Html;

use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;

/**
 * Interface UiRendererInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface UiRendererInterface
{
    /**
     * Renders the triggers for the widget of the specified widget state to an array
     *
     * @param WidgetStatePathInterface $widgetStatePath
     * @param ProgramInterface $program
     * @return array
     */
    public function renderTriggers(
        WidgetStatePathInterface $widgetStatePath,
        ProgramInterface $program
    );

    /**
     * Renders the specified widget to a HTML node
     *
     * @param WidgetStatePathInterface $widgetStatePath
     * @param ProgramInterface $program
     * @return HtmlNodeInterface
     */
    public function renderWidget(
        WidgetStatePathInterface $widgetStatePath,
        ProgramInterface $program
    );
}
