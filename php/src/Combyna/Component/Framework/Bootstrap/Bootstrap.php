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
 * Interface BootstrapInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @var string|null
     */
    private $cachePath;

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
     * @param BootstrapConfigInterface|null $config
     * @param string|null $originator
     * @param string|null $cachePath
     * @param bool $debug
     */
    public function __construct(
        BootstrapConfigInterface $config = null,
        $originator = null,
        $cachePath = null,   // Path to the directory where the compiled container etc. will be stored
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

        $this->cachePath = $cachePath;
        $this->config = $config;
        $this->debug = $debug;
        $this->originator = $originator;
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
                $this->cachePath
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
