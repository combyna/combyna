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
use Combyna\Component\Plugin\LibraryConfigCollection;
use Combyna\Component\Program\Validation\Validator\NodeValidatorInterface;

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
     * @var LibraryConfigCollection
     */
    private $libraryConfigCollection;

    /**
     * @param EnvironmentFactoryInterface $environmentFactory
     * @param EnvironmentLoaderInterface $environmentLoader
     * @param AppLoaderInterface $appLoader
     * @param NodeValidatorInterface $appValidator
     * @param AppNodePromoter $appNodePromoter
     * @param LibraryConfigCollection $libraryConfigCollection
     */
    public function __construct(
        EnvironmentFactoryInterface $environmentFactory,
        EnvironmentLoaderInterface $environmentLoader,
        AppLoaderInterface $appLoader,
        NodeValidatorInterface $appValidator,
        AppNodePromoter $appNodePromoter,
        LibraryConfigCollection $libraryConfigCollection
    ) {
        $this->appLoader = $appLoader;
        $this->appNodePromoter = $appNodePromoter;
        $this->appValidator = $appValidator;
        $this->environmentFactory = $environmentFactory;
        $this->environmentLoader = $environmentLoader;
        $this->libraryConfigCollection = $libraryConfigCollection;
    }

    /**
     * Creates an app from its config array
     *
     * @param array $appConfig
     * @param EnvironmentNode|null $environmentNode
     * @return AppInterface
     */
    public function createApp(array $appConfig, EnvironmentNode $environmentNode = null)
    {
        if ($environmentNode === null) {
            $environmentNode = $this->createEnvironment();
        }

        $appNode = $this->appLoader->loadApp($environmentNode, $appConfig);

        $validationContext = $this->appValidator->validate($appNode, $appNode);
        $validationContext->throwIfViolated();

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
            $environmentConfig['libraries'],
            $this->libraryConfigCollection->getLibraryConfigs()
        );

        $environmentNode = $this->environmentLoader->loadEnvironment($environmentConfig);

        return $environmentNode;
    }
}
