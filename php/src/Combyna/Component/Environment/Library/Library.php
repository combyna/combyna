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

use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;
use Combyna\Component\Event\EventDefinitionCollectionInterface;
use Combyna\Component\Signal\SignalDefinitionCollectionInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;

/**
 * Class Library
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Library implements LibraryInterface
{
    /**
     * @var EventDefinitionCollectionInterface
     */
    private $eventDefinitionCollection;

    /**
     * @var FunctionCollectionInterface
     */
    private $functionCollection;

    /**
     * @var string
     */
    private $name;

    /**
     * @var SignalDefinitionCollectionInterface
     */
    private $signalDefinitionCollection;

    /**
     * @var array
     */
    private $translations;

    /**
     * @var WidgetDefinitionInterface[]
     */
    private $widgetDefinitions = [];

    /**
     * @param string $name
     * @param FunctionCollectionInterface $functionCollection
     * @param EventDefinitionCollectionInterface $eventDefinitionCollection
     * @param SignalDefinitionCollectionInterface $signalDefinitionCollection
     * @param WidgetDefinitionInterface[] $widgetDefinitions
     * @param array $translations
     */
    public function __construct(
        $name,
        FunctionCollectionInterface $functionCollection,
        EventDefinitionCollectionInterface $eventDefinitionCollection,
        SignalDefinitionCollectionInterface $signalDefinitionCollection,
        array $widgetDefinitions = [],
        array $translations = []
    ) {
        $this->eventDefinitionCollection = $eventDefinitionCollection;
        $this->functionCollection = $functionCollection;
        $this->name = $name;
        $this->signalDefinitionCollection = $signalDefinitionCollection;
        $this->translations = $translations;

        // Index the widget definitions by name to simplify lookups
        foreach ($widgetDefinitions as $widgetDefinition) {
            $this->widgetDefinitions[$widgetDefinition->getName()] = $widgetDefinition;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDefinitionByName($eventName)
    {
        return $this->eventDefinitionCollection->getByName($eventName);
    }

    /**
     * {@inheritdoc]
     */
    public function getGenericFunctionByName($functionName)
    {
        $function = $this->functionCollection->getByName($functionName);

        // TODO: Check type of function and throw IncorrectFunctionTypeException if wrong

        return $function;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getSignalDefinitionByName($signalName)
    {
        return $this->signalDefinitionCollection->getByName($signalName);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionByName($widgetDefinitionName)
    {
        if (!array_key_exists($widgetDefinitionName, $this->widgetDefinitions)) {
            throw new WidgetDefinitionNotSupportedException($this, $widgetDefinitionName);
        }

        return $this->widgetDefinitions[$widgetDefinitionName];
    }
}
