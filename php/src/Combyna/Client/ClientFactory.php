<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Client;

use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\ArrayRenderer;

/**
 * Class ClientFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ClientFactory
{
    /**
     * @var ArrayRenderer
     */
    private $arrayRenderer;

    /**
     * @var Combyna
     */
    private $combyna;

    /**
     * @param Combyna $combyna
     * @param ArrayRenderer $arrayRenderer
     */
    public function __construct(Combyna $combyna, ArrayRenderer $arrayRenderer)
    {
        $this->arrayRenderer = $arrayRenderer;
        $this->combyna = $combyna;
    }

    /**
     * Creates a new Client from the given environment and app configuration
     *
     * @param array $environmentConfig
     * @param array $appConfig
     * @return Client
     */
    public function createClient(array $environmentConfig, array $appConfig)
    {
        $environment = $this->combyna->createEnvironment($environmentConfig);
        $app = $this->combyna->createApp($appConfig, $environment);

        return new Client($this->arrayRenderer, $app);
    }
}
