<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Framework\EventDispatcher\Event;

use Combyna\Component\Environment\Config\Act\EnvironmentNode;

/**
 * Class EnvironmentLoadedEvent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentLoadedEvent extends AbstractFrameworkEvent
{
    /**
     * @var EnvironmentNode
     */
    private $environmentNode;

    /**
     * @param EnvironmentNode $environmentNode
     */
    public function __construct(EnvironmentNode $environmentNode)
    {
        $this->environmentNode = $environmentNode;
    }

    /**
     * Fetches the EnvironmentNode that was created
     *
     * @return EnvironmentNode
     */
    public function getEnvironmentNode()
    {
        return $this->environmentNode;
    }
}
