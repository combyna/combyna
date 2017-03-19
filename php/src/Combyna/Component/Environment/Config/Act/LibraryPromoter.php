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

use Combyna\Component\Environment\Library\LibraryFactoryInterface;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionPromoter;

/**
 * Class LibraryPromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class LibraryPromoter
{
    /**
     * @var FunctionPromoter
     */
    private $functionPromoter;

    /**
     * @var LibraryFactoryInterface
     */
    private $libraryFactory;

    /**
     * @var WidgetDefinitionPromoter
     */
    private $widgetDefinitionPromoter;

    /**
     * @param FunctionPromoter $functionPromoter
     * @param WidgetDefinitionPromoter $widgetDefinitionPromoter
     * @param LibraryFactoryInterface $libraryFactory
     */
    public function __construct(
        FunctionPromoter $functionPromoter,
        WidgetDefinitionPromoter $widgetDefinitionPromoter,
        LibraryFactoryInterface $libraryFactory
    ) {
        $this->functionPromoter = $functionPromoter;
        $this->libraryFactory = $libraryFactory;
        $this->widgetDefinitionPromoter = $widgetDefinitionPromoter;
    }

    /**
     * Creates a Library from its ACT node
     *
     * @param LibraryNode $libraryNode
     * @return LibraryInterface
     */
    public function promoteLibrary(LibraryNode $libraryNode)
    {
        $functions = [];

        foreach ($libraryNode->getFunctions() as $functionNode) {
            $functions[] = $this->functionPromoter->promoteFunction($functionNode);
        }

        $widgetDefinitions = [];

        foreach ($libraryNode->getWidgetDefinitions() as $widgetDefinitionNode) {
            $widgetDefinitions[] = $this->widgetDefinitionPromoter->promoteDefinition($widgetDefinitionNode);
        }

        return $this->libraryFactory->create($libraryNode->getName(), $functions, $widgetDefinitions);
    }
}
