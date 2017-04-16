<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Library;

use Combyna\Component\Environment\Exception\FunctionNotSupportedException;
use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;
use Combyna\Component\Ui\WidgetDefinitionInterface;

/**
 * Class Library
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Library implements LibraryInterface
{
    /**
     * @var FunctionInterface[]
     */
    private $functions = [];

    /**
     * @var string
     */
    private $name;

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
     * @param FunctionInterface[] $functions
     * @param WidgetDefinitionInterface[] $widgetDefinitions
     * @param array $translations
     */
    public function __construct($name, array $functions = [], array $widgetDefinitions = [], array $translations = [])
    {
        $this->name = $name;

        // Index the functions by name to simplify lookups
        foreach ($functions as $function) {
            $this->functions[$function->getName()] = $function;
        }

        $this->translations = $translations;

        // Index the widget functions by name to simplify lookups
        foreach ($widgetDefinitions as $widgetDefinition) {
            $this->widgetDefinitions[$widgetDefinition->getName()] = $widgetDefinition;
        }
    }

    /**
     * {@inheritdoc]
     */
    public function getGenericFunction($functionName)
    {
        if (!array_key_exists($functionName, $this->functions)) {
            throw new FunctionNotSupportedException($this->name, $functionName);
        }

        // TODO: Check type of function and throw IncorrectFunctionTypeException if wrong

        return $this->functions[$functionName];
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
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinition($widgetDefinitionName)
    {
        if (!array_key_exists($widgetDefinitionName, $this->widgetDefinitions)) {
            throw new WidgetDefinitionNotSupportedException($this, $widgetDefinitionName);
        }

        return $this->widgetDefinitions[$widgetDefinitionName];
    }
}
