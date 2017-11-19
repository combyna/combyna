<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Act;

use Combyna\Component\Environment\EnvironmentFactoryInterface;
use Combyna\Component\Environment\EnvironmentInterface;

/**
 * Class EnvironmentNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentNodePromoter
{
    /**
     * @var EnvironmentFactoryInterface
     */
    private $environmentFactory;

    /**
     * @var LibraryNodePromoter
     */
    private $libraryPromoter;

    /**
     * @param EnvironmentFactoryInterface $environmentFactory
     * @param LibraryNodePromoter $libraryPromoter
     */
    public function __construct(EnvironmentFactoryInterface $environmentFactory, LibraryNodePromoter $libraryPromoter)
    {
        $this->environmentFactory = $environmentFactory;
        $this->libraryPromoter = $libraryPromoter;
    }

    /**
     * Creates an Environment from its ACT node
     *
     * @param EnvironmentNode $environmentNode
     * @return EnvironmentInterface
     */
    public function promoteEnvironment(EnvironmentNode $environmentNode)
    {
        $environment = $this->environmentFactory->create();

        foreach ($environmentNode->getLibraries() as $libraryNode) {
            $environment->installLibrary($this->libraryPromoter->promoteLibrary($libraryNode, $environment));
        }

        return $environment;
    }
}
