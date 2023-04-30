<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Framework;

/**
 * Class FrameworkEvents
 *
 * Contains all events dispatched by the Framework component
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
final class FrameworkEvents
{
    const APP_STATE_UPDATED = 'combyna.app_state_updated';

    const ENVIRONMENT_LOADED = 'combyna.environment_loaded';
}
