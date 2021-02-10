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
use Combyna\Component\Trigger\TriggerInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use LogicException;

/**
 * Class TriggerRenderer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TriggerRenderer implements TriggerRendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function renderTriggers(
        WidgetStatePathInterface $widgetStatePath,
        ProgramInterface $program
    ) {
        $widget = $program->getWidgetByPath($widgetStatePath->getWidgetPath());

        if (!$widget instanceof DefinedWidgetInterface) {
            throw new LogicException(sprintf(
                'Expected a %s, got a %s',
                DefinedWidgetInterface::class,
                get_class($widget)
            ));
        }

        return array_map(
            function (TriggerInterface $trigger) {
                return [
                    'library' => $trigger->getEventLibraryName(),
                    'event' => $trigger->getEventName()
                ];
            },
            $widget->getTriggers()->getAll()
        );
    }
}
