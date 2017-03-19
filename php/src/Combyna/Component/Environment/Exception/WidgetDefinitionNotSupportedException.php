<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Exception;

use Combyna\Component\Environment\Library\LibraryInterface;
use Exception;

/**
 * Class WidgetDefinitionNotInstalledException
 *
 * Thrown when an attempt is made to fetch a widget definition from a library
 * when that library implements no such widget definition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionNotSupportedException extends Exception
{
    /**
     * @var LibraryInterface
     */
    private $library;

    /**
     * @var string
     */
    private $widgetDefinitionName;

    /**
     * @param LibraryInterface $library
     * @param string $widgetDefinitionName
     */
    public function __construct(LibraryInterface $library, $widgetDefinitionName)
    {
        parent::__construct(
            'Library "' . $library->getName() . '" does not define widget definition "' . $widgetDefinitionName . '"'
        );

        $this->library = $library;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * Fetches the library that does not support the requested widget definition
     *
     * @return LibraryInterface
     */
    public function getLibrary()
    {
        return $this->library;
    }

    /**
     * Fetches the name of the unsupported widget definition
     *
     * @return string
     */
    public function getWidgetDefinitionName()
    {
        return $this->widgetDefinitionName;
    }
}
