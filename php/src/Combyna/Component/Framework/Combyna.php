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

use Combyna\Component\App\AppInterface;
use Combyna\Component\App\Config\Act\AppNodePromoter;
use Combyna\Component\App\Config\Loader\AppLoaderInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Config\Loader\EnvironmentLoaderInterface;
use Combyna\Component\Environment\EnvironmentFactoryInterface;
use Combyna\Component\Framework\Context\ModeContext;
use Combyna\Component\Framework\EventDispatcher\Event\EnvironmentLoadedEvent;
use Combyna\Component\Plugin\LibraryConfigCollection;
use Combyna\Component\Program\Validation\Validator\NodeValidatorInterface;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use LogicException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Combyna
 *
 * An entrypoint facade for creating a Combyna app and its basic dependencies
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Combyna
{
    /**
     * @var bool
     */
    private $appCreated = false;

    /**
     * @var AppLoaderInterface
     */
    private $appLoader;

    /**
     * @var AppNodePromoter
     */
    private $appNodePromoter;

    /**
     * @var NodeValidatorInterface
     */
    private $appValidator;

    /**
     * @var EnvironmentFactoryInterface
     */
    private $environmentFactory;

    /**
     * @var EnvironmentLoaderInterface
     */
    private $environmentLoader;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var LibraryConfigCollection
     */
    private $libraryConfigCollection;

    /**
     * @var ModeContext
     */
    private $modeContext;

    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * @param ContainerInterface $serviceContainer
     * @param EventDispatcherInterface $eventDispatcher
     * @param EnvironmentFactoryInterface $environmentFactory
     * @param EnvironmentLoaderInterface $environmentLoader
     * @param AppLoaderInterface $appLoader
     * @param NodeValidatorInterface $appValidator
     * @param AppNodePromoter $appNodePromoter
     * @param LibraryConfigCollection $libraryConfigCollection
     * @param ModeContext $modeContext
     */
    public function __construct(
        ContainerInterface $serviceContainer,
        EventDispatcherInterface $eventDispatcher,
        EnvironmentFactoryInterface $environmentFactory,
        EnvironmentLoaderInterface $environmentLoader,
        AppLoaderInterface $appLoader,
        NodeValidatorInterface $appValidator,
        AppNodePromoter $appNodePromoter,
        LibraryConfigCollection $libraryConfigCollection,
        ModeContext $modeContext
    ) {
        $this->appLoader = $appLoader;
        $this->appNodePromoter = $appNodePromoter;
        $this->appValidator = $appValidator;
        $this->environmentFactory = $environmentFactory;
        $this->environmentLoader = $environmentLoader;
        $this->eventDispatcher = $eventDispatcher;
        $this->libraryConfigCollection = $libraryConfigCollection;
        $this->modeContext = $modeContext;
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * Creates an app from its config array
     *
     * @param array $appConfig
     * @param EnvironmentNode|null $environmentNode
     * @return AppInterface
     * @throws ValidationFailureException
     */
    public function createApp(array $appConfig, EnvironmentNode $environmentNode = null)
    {
        $this->appCreated = true;

        if ($environmentNode === null) {
            $environmentNode = $this->createEnvironment();
        }

        $appNode = $this->appLoader->loadApp($environmentNode, $appConfig);

        // For development environments, statically validate the app for correctness before continuing
        if ($this->modeContext->getMode()->isDevelopment()) {
            $validationContext = $this->appValidator->validate($appNode, $appNode);
            $validationContext->throwIfViolated();
        }

        return $this->appNodePromoter->promoteApp($appNode, $environmentNode);
    }

    /**
     * Creates an environment node
     *
     * @param array $environmentConfig
     * @return EnvironmentNode
     */
    public function createEnvironment(array $environmentConfig = [])
    {
        if (!array_key_exists('libraries', $environmentConfig)) {
            $environmentConfig['libraries'] = [];
        }

        $environmentConfig['libraries'] = array_merge(
            $this->libraryConfigCollection->getLibraryConfigs(),
            $environmentConfig['libraries']
        );

        $environmentNode = $this->environmentLoader->loadEnvironment($environmentConfig);

        // Dispatch an event to provide an extension point for automatically installing native functions, etc.
        $this->eventDispatcher->dispatch(
            FrameworkEvents::ENVIRONMENT_LOADED,
            new EnvironmentLoadedEvent(
                $environmentNode
            )
        );

        return $environmentNode;
    }

    /**
     * Fetches the service container. This allows external code to inspect and modify
     * the service container (in debug mode) as needed.
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->serviceContainer;
    }

    /**
     * Switches to production mode (non-reversible, and can only be done before any app is loaded)
     */
    public function useProductionMode()
    {
        if ($this->appCreated) {
            throw new LogicException('Unable to switch to production mode, as an app has already been created');
        }

        $this->modeContext->useProductionMode();
    }
}
