<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Library;

/**
 * Class WidgetValueProviderLocator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetValueProviderLocator implements WidgetValueProviderLocatorInterface
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $valueName;

    /**
     * @var string
     */
    private $widgetDefinitionName;

    /**
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param string $valueName
     * @param callable $callable
     */
    public function __construct($libraryName, $widgetDefinitionName, $valueName, callable $callable)
    {
        $this->callable = $callable;
        $this->libraryName = $libraryName;
        $this->valueName = $valueName;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueName()
    {
        return $this->valueName;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return $this->widgetDefinitionName;
    }
}
