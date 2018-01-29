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

        $libraries = $environmentNode->getLibraries();

        // Sort the libraries so that any that are depended on are loaded before their dependencies
        if (function_exists('usort')) { // FIXME: Implement usort(...) in Uniter
            usort($libraries, function (LibraryNode $libraryA, LibraryNode $libraryB) {
                return $libraryB->referencesLibrary($libraryA->getName()) ? 1 : -1;
            });
        }

        foreach ($libraries as $libraryNode) {
            $environment->installLibrary($this->libraryPromoter->promoteLibrary($libraryNode, $environment));
        }

        return $environment;
    }
}
