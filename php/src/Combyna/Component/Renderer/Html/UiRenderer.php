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
use Combyna\Component\Renderer\Html\WidgetRenderer\DelegatingWidgetRenderer;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;

/**
 * Class UiRenderer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UiRenderer implements UiRendererInterface
{
    /**
     * @var TriggerRendererInterface
     */
    private $triggerRenderer;

    /**
     * @var DelegatingWidgetRenderer
     */
    private $widgetRenderer;

    /**
     * @param TriggerRendererInterface $triggerRenderer
     * @param DelegatingWidgetRenderer $widgetRenderer
     */
    public function __construct(
        TriggerRendererInterface $triggerRenderer,
        DelegatingWidgetRenderer $widgetRenderer
    ) {
        $this->triggerRenderer = $triggerRenderer;
        $this->widgetRenderer = $widgetRenderer;
    }

    /**
     * {@inheritdoc}
     */
    public function renderTriggers(WidgetStatePathInterface $widgetStatePath, ProgramInterface $program)
    {
        return $this->triggerRenderer->renderTriggers($widgetStatePath, $program);
    }

    /**
     * {@inheritdoc}
     */
    public function renderWidget(
        WidgetStatePathInterface $widgetStatePath,
        ProgramInterface $program
    ) {
        return $this->widgetRenderer->renderWidget($widgetStatePath, $program);
    }
}
