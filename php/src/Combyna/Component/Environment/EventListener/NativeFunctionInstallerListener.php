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

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Common\Delegator\DelegatorInterface;
use Combyna\Component\Environment\Exception\FunctionNotSupportedException;
use Combyna\Component\Environment\Exception\LibraryNotInstalledException;
use Combyna\Component\Environment\Library\NativeFunctionProviderInterface;
use Combyna\Component\Framework\EventDispatcher\Event\EnvironmentLoadedEvent;

/**
 * Class NativeFunctionInstallerListener
 *
 * Installs native functions from all registered native function providers
 * when a new EnvironmentNode is created
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NativeFunctionInstallerListener implements DelegatorInterface
{
    /**
     * @var NativeFunctionProviderInterface[]
     */
    private $providers = [];

    /**
     * Adds a new provider
     *
     * @param NativeFunctionProviderInterface $provider
     */
    public function addProvider(NativeFunctionProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * Called when the environment is loaded
     *
     * @param EnvironmentLoadedEvent $event
     * @throws FunctionNotSupportedException
     * @throws LibraryNotInstalledException
     */
    public function onEnvironmentLoaded(EnvironmentLoadedEvent $event)
    {
        $environmentNode = $event->getEnvironmentNode();

        foreach ($this->providers as $provider) {
            foreach ($provider->getNativeFunctionLocators() as $locator) {
                $environmentNode->installNativeFunction(
                    $locator->getLibraryName(),
                    $locator->getFunctionName(),
                    function (StaticBagInterface $argumentStaticBag) use ($locator) {
                        // Unpack the Combyna-style argument bag into a PHP-style argument list
                        $args = array_map(function ($parameterName) use ($argumentStaticBag) {
                            return $argumentStaticBag->getStatic($parameterName);
                        }, $locator->getParameterNamesInArgumentOrder());

                        return call_user_func_array($locator->getCallable(), $args);
                    }
                );
            }
        }
    }
}
