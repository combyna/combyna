<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Framework\Bootstrap;

use Combyna\CombynaBootstrap;
use Combyna\CombynaBootstrapInterface;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Bootstrap
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @var BootstrapConfigInterface
     */
    private $config;

    /**
     * @var CombynaBootstrapInterface|null
     */
    private $combynaBootstrap = null;

    /**
     * @var ContainerInterface|null
     */
    private $container = null;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var string
     */
    private $originator;

    /**
     * @var string|null
     */
    private $relativeCachePath;

    /**
     * @var string|null
     */
    private $rootPath;

    /**
     * @param BootstrapConfigInterface|null $config
     * @param string|null $originator
     * @param string|null $rootPath
     * @param string|null $relativeCachePath
     * @param bool $debug
     */
    public function __construct(
        BootstrapConfigInterface $config = null,
        $originator = null,
        // Path to the project directory
        $rootPath = null,
        // Path to the directory where the compiled container etc. will be stored,
        // relative to the root path
        $relativeCachePath = null,
        $debug = false // Whether debug mode is enabled
    ) {
        if ($config === null) {
            // CombynaBundle's CombynaExtension would set this correctly
            throw new InvalidArgumentException(
                'Missing bootstrap config - if you are using Symfony, consider using CombynaBundle'
            );
        }

        if ($originator === null) {
            throw new InvalidArgumentException(
                'Missing originator - must be either "client" or "server"'
            );
        }

        $this->config = $config;
        $this->debug = $debug;
        $this->originator = $originator;
        $this->relativeCachePath = $relativeCachePath;
        $this->rootPath = $rootPath;
    }

    /**
     * @return CombynaBootstrapInterface
     */
    private function getCombynaBootstrap()
    {
        if ($this->combynaBootstrap === null) {
            // First time the container has been accessed - create it
            $this->combynaBootstrap = new CombynaBootstrap(
                $this->config->getPlugins(),
                $this->originator,
                $this->debug,
                $this->rootPath,
                $this->relativeCachePath
            );
        }

        return $this->combynaBootstrap;
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        if ($this->container === null) {
            $this->container = $this->getCombynaBootstrap()->createContainer();
        }

        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerBuilder()
    {
        return $this->getCombynaBootstrap()->getContainerBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp()
    {
        $this->getCombynaBootstrap()->warmUp();
    }
}
