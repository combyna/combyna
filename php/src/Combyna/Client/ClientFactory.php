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

use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\ArrayRenderer;
use Combyna\Component\Validator\Exception\ValidationFailureException;

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
     * @throws ValidationFailureException
     */
    public function createClient(array $environmentConfig, array $appConfig)
    {
        $environmentNode = $this->combyna->createEnvironment($environmentConfig);
        $app = $this->combyna->createApp($appConfig, $environmentNode);

        return new Client($this->arrayRenderer, $app);
    }

    /**
     * Switches to production mode (non-reversible, and can only be done before any app is loaded)
     */
    public function useProductionMode()
    {
        $this->combyna->useProductionMode();
    }
}
