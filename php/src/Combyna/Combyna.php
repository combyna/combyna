<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna;

use Combyna\Component\App\AppInterface;
use Combyna\Component\App\Config\Act\AppNodePromoter;
use Combyna\Component\App\Config\Loader\AppLoaderInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Config\Loader\EnvironmentLoaderInterface;
use Combyna\Component\Environment\EnvironmentFactoryInterface;
use Combyna\Component\Validator\ValidatorInterface;

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
     * @var EnvironmentFactoryInterface
     */
    private $environmentFactory;

    /**
     * @var EnvironmentLoaderInterface
     */
    private $environmentLoader;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param EnvironmentFactoryInterface $environmentFactory
     * @param EnvironmentLoaderInterface $environmentLoader
     * @param AppLoaderInterface $appLoader
     * @param ValidatorInterface $validator
     * @param AppNodePromoter $appNodePromoter
     */
    public function __construct(
        EnvironmentFactoryInterface $environmentFactory,
        EnvironmentLoaderInterface $environmentLoader,
        AppLoaderInterface $appLoader,
        ValidatorInterface $validator,
        AppNodePromoter $appNodePromoter
    ) {
        $this->appLoader = $appLoader;
        $this->appNodePromoter = $appNodePromoter;
        $this->environmentFactory = $environmentFactory;
        $this->environmentLoader = $environmentLoader;
        $this->validator = $validator;
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

        $validationContext = $this->validator->validate($environmentNode, $environmentNode);
        $validationContext->throwIfViolated();
        $validationContext = $this->validator->validate($appNode, $environmentNode);
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
        $environmentNode = $this->environmentLoader->loadEnvironment($environmentConfig);

        return $environmentNode;
    }
}
