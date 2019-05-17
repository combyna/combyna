<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Widget;

use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;

/**
 * Class WidgetDefinitionCollection
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionCollection implements WidgetDefinitionCollectionInterface
{
    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var WidgetDefinitionInterface[]
     */
    private $widgetDefinitions = [];

    /**
     * @param WidgetDefinitionInterface[] $widgetDefinitions
     * @param string $libraryName
     */
    public function __construct(array $widgetDefinitions, $libraryName)
    {
        $this->libraryName = $libraryName;

        // Index the widget definitions by name to simplify lookups
        foreach ($widgetDefinitions as $widgetDefinition) {
            $this->widgetDefinitions[$widgetDefinition->getName()] = $widgetDefinition;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getByName($widgetName)
    {
        if (!array_key_exists($widgetName, $this->widgetDefinitions)) {
            throw new WidgetDefinitionNotSupportedException($this->libraryName, $widgetName);
        }

        return $this->widgetDefinitions[$widgetName];
    }
}
