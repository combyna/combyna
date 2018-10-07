<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class WidgetDefinitionHasValueQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionHasValueQuery implements BooleanQueryInterface
{
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
     */
    public function __construct($libraryName, $widgetDefinitionName, $valueName)
    {
        $this->libraryName = $libraryName;
        $this->valueName = $valueName;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResult()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return sprintf(
            'Whether the widget definition "%s" of library "%s" defines value "%s"',
            $this->widgetDefinitionName,
            $this->libraryName,
            $this->valueName
        );
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
     * Fetches the name of the value to determine the existence of
     *
     * @return string
     */
    public function getValueName()
    {
        return $this->valueName;
    }

    /**
     * Fetches the name of the widget definition
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
