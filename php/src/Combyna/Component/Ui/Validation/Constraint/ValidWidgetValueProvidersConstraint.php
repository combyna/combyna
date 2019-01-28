<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Constraint;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;

/**
 * Class ValidWidgetValueProvidersConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidWidgetValueProvidersConstraint implements ConstraintInterface
{
    /**
     * @var callable
     */
    private $getValueNameToProviderCallableMap;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string[]
     */
    private $valueNames;

    /**
     * @var string
     */
    private $widgetDefinitionName;

    /**
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param string[] $valueNames
     * @param callable $getValueNameToProviderCallableMap
     */
    public function __construct(
        $libraryName,
        $widgetDefinitionName,
        array $valueNames,
        callable $getValueNameToProviderCallableMap
    ) {
        $this->getValueNameToProviderCallableMap = $getValueNameToProviderCallableMap;
        $this->libraryName = $libraryName;
        $this->valueNames = $valueNames;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * Fetches the name of the library that defines the widget definition
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the names of the values of the widget definition
     *
     * @return string[]
     */
    public function getValueNames()
    {
        return $this->valueNames;
    }

    /**
     * Fetches the map of widget value names to provider callables
     *
     * @return callable[]
     */
    public function getValueNameToProviderCallableMap()
    {
        $getValueNameToProviderCallableMap = $this->getValueNameToProviderCallableMap;

        return $getValueNameToProviderCallableMap();
    }

    /**
     * Fetches the name of the library that defines the widget definition
     *
     * @return string
     */
    public function getWidgetDefinitionName()
    {
        return $this->widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
