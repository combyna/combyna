<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\EventListener;

use Combyna\Component\Common\Delegator\DelegatorInterface;
use Combyna\Component\Environment\Exception\LibraryNotInstalledException;
use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;
use Combyna\Component\Environment\Library\WidgetValueProviderProviderInterface;
use Combyna\Component\Framework\EventDispatcher\Event\EnvironmentLoadedEvent;

/**
 * Class WidgetValueProviderInstallerListener
 *
 * Installs widget providers from all registered widget provider-providers
 * when a new EnvironmentNode is created
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetValueProviderInstallerListener implements DelegatorInterface
{
    /**
     * @var WidgetValueProviderProviderInterface[]
     */
    private $providers = [];

    /**
     * Adds a new provider
     *
     * @param WidgetValueProviderProviderInterface $provider
     */
    public function addProvider(WidgetValueProviderProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * Called when the environment is loaded
     *
     * @param EnvironmentLoadedEvent $event
     * @throws LibraryNotInstalledException
     * @throws WidgetDefinitionNotSupportedException
     */
    public function onEnvironmentLoaded(EnvironmentLoadedEvent $event)
    {
        $environmentNode = $event->getEnvironmentNode();

        foreach ($this->providers as $provider) {
            foreach ($provider->getWidgetValueProviderLocators() as $locator) {
                $environmentNode->installWidgetValueProvider(
                    $locator->getLibraryName(),
                    $locator->getWidgetDefinitionName(),
                    $locator->getValueName(),
                    $locator->getCallable()
                );
            }
        }
    }
}
