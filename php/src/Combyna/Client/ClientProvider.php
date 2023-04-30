<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Client;

use Combyna\Component\Common\Exception\Exception;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\ArrayRenderer;
use Combyna\Component\Ui\Environment\Library\GenericWidgetValueProviderInterface;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ClientProvider.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ClientProvider
{
    /**
     * @var ArrayRenderer
     */
    private $arrayRenderer;

    /**
     * @var bool
     */
    private $clientProvided = false;

    /**
     * @var Combyna
     */
    private $combyna;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var GenericWidgetValueProviderInterface
     */
    private $widgetValueProvider;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param Combyna $combyna
     * @param ArrayRenderer $arrayRenderer
     * @param GenericWidgetValueProviderInterface $widgetValueProvider
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Combyna $combyna,
        ArrayRenderer $arrayRenderer,
        GenericWidgetValueProviderInterface $widgetValueProvider
    ) {
        $this->arrayRenderer = $arrayRenderer;
        $this->combyna = $combyna;
        $this->eventDispatcher = $eventDispatcher;
        $this->widgetValueProvider = $widgetValueProvider;
    }

    /**
     * Adds a provider for a specific widget value of a primitive widget definition
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param string $valueName
     * @param callable $callable
     */
    public function addWidgetValueProvider($libraryName, $widgetDefinitionName, $valueName, callable $callable)
    {
        $this->widgetValueProvider->addProvider($libraryName, $widgetDefinitionName, $valueName, $callable);
    }

    /**
     * Creates a new Client from the given environment and app configuration
     *
     * @param array $environmentConfig
     * @param array $appConfig
     * @return Client
     * @throws ValidationFailureException
     */
    public function createClient(array $environmentConfig, array $appConfig)
    {
        if ($this->clientProvided) {
            throw new Exception('Client was already provided');
        }

        $this->clientProvided = true;

        $environmentNode = $this->combyna->createEnvironment($environmentConfig);
        $app = $this->combyna->createApp($appConfig, $environmentNode);
        $initialAppState = $app->createInitialState();

        return new Client(
            $this->eventDispatcher,
            $this->combyna,
            $this->arrayRenderer,
            $app,
            $initialAppState
        );
    }

    /**
     * Fetches the service container. This allows external code to inspect and modify
     * the service container (in debug mode) as needed.
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->combyna->getContainer();
    }

    /**
     * Switches to production mode (non-reversible, and can only be done before any app is loaded)
     */
    public function useProductionMode()
    {
        $this->combyna->useProductionMode();
    }
}
