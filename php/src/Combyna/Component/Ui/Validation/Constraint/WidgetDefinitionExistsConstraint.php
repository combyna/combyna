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
 * Class WidgetDefinitionExistsConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionExistsConstraint implements ConstraintInterface
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
        $this->libraryName = $libraryName;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * Fetches the name of the library that should define the widget definition
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
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
