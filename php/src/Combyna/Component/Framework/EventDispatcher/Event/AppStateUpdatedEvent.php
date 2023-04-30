<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Framework\EventDispatcher\Event;

use Combyna\Component\App\State\AppStateInterface;

/**
 * Class AppStateUpdatedEvent.
 *
 * Dispatched when a new state is available for the application.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppStateUpdatedEvent extends AbstractFrameworkEvent
{
    /**
     * @var AppStateInterface
     */
    private $appState;

    /**
     * @param AppStateInterface $appState
     */
    public function __construct(AppStateInterface $appState)
    {
        $this->appState = $appState;
    }

    /**
     * Fetches the new AppState.
     *
     * @return AppStateInterface
     */
    public function getAppState()
    {
        return $this->appState;
    }
}
