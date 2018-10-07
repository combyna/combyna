<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Exception;

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
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $widgetDefinitionName;

    /**
     * @param string $libraryName
     * @param string $widgetDefinitionName
     */
    public function __construct($libraryName, $widgetDefinitionName)
    {
        parent::__construct(
            sprintf(
                'Library "%s" does not define widget definition "%s"',
                $libraryName,
                $widgetDefinitionName
            )
        );

        $this->libraryName = $libraryName;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * Fetches the name of the library that does not support the requested widget definition
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
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
