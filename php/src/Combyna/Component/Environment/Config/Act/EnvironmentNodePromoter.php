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
use Combyna\Component\Environment\Exception\LibraryAlreadyInstalledException;
use Combyna\Component\Environment\Library\LibraryInterface;

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
     * @throws LibraryAlreadyInstalledException
     */
    public function promoteEnvironment(EnvironmentNode $environmentNode)
    {
        $environment = $this->environmentFactory->create();

        $libraryNodes = $environmentNode->getLibraries();

        // Sort the libraries so that any that are depended on are promoted before their dependencies
        if (function_exists('uasort')) { // FIXME: Implement uasort(...) in Uniter
            uasort($libraryNodes, function (LibraryNode $libraryNodeA, LibraryNode $libraryNodeB) {
                if ($libraryNodeA->getName() === LibraryInterface::CORE) {
                    // Always load the special Core library first
                    return -1;
                }

                return $libraryNodeB->referencesLibrary($libraryNodeA->getName()) ? -1 : 1;
            });
        }

        foreach ($libraryNodes as $libraryNode) {
            $environment->installLibrary($this->libraryPromoter->promoteLibrary($libraryNode, $environment));
        }

        return $environment;
    }
}
