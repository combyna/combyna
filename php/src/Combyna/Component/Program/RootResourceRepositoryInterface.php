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

use Combyna\Component\Signal\SignalDefinitionRepositoryInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionRepositoryInterface;

/**
 * Interface RootResourceRepositoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RootResourceRepositoryInterface extends ResourceRepositoryInterface
{
    /**
     * Sets the signal definition repository to use
     *
     * @param SignalDefinitionRepositoryInterface $signalDefinitionRepository
     */
    public function setSignalDefinitionRepository(SignalDefinitionRepositoryInterface $signalDefinitionRepository);

    /**
     * Sets the widget definition repository to use
     *
     * @param WidgetDefinitionRepositoryInterface $widgetDefinitionRepository
     */
    public function setWidgetDefinitionRepository(WidgetDefinitionRepositoryInterface $widgetDefinitionRepository);
}
