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

use Combyna\Component\Common\AbstractComponent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * Class FrameworkComponent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FrameworkComponent extends AbstractComponent
{
    const EVENT_DISPATCHER_SERVICE_ID = 'combyna.framework.event_dispatcher';
    const EVENT_LISTENER_TAG = 'combyna.event_listener';
    const EVENT_SUBSCRIBER_TAG = 'combyna.event_subscriber';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        // Allow event listener and subscriber services to register themselves as services
        $containerBuilder->addCompilerPass(
            new RegisterListenersPass(
                self::EVENT_DISPATCHER_SERVICE_ID,
                self::EVENT_LISTENER_TAG,
                self::EVENT_SUBSCRIBER_TAG
            )
        );
    }
}
