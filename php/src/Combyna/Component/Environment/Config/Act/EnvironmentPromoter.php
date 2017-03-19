<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Act;

use Combyna\Component\Environment\EnvironmentFactoryInterface;
use Combyna\Component\Environment\EnvironmentInterface;

/**
 * Class EnvironmentPromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EnvironmentPromoter
{
    /**
     * @var EnvironmentFactoryInterface
     */
    private $environmentFactory;

    /**
     * @var LibraryPromoter
     */
    private $libraryPromoter;

    /**
     * @param EnvironmentFactoryInterface $environmentFactory
     * @param LibraryPromoter $libraryPromoter
     */
    public function __construct(EnvironmentFactoryInterface $environmentFactory, LibraryPromoter $libraryPromoter)
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
        $libraries = [];

        foreach ($environmentNode->getLibraries() as $libraryNode) {
            $libraries[] = $this->libraryPromoter->promoteLibrary($libraryNode);
        }

        return $this->environmentFactory->create($libraries);
    }
}
