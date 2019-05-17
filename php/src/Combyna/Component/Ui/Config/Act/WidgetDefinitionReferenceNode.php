<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Ui\Validation\Constraint\WidgetDefinitionExistsConstraint;

/**
 * Class WidgetDefinitionReferenceNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionReferenceNode extends AbstractActNode
{
    const TYPE = 'widget-definition-reference';

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
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(
            new WidgetDefinitionExistsConstraint(
                $this->libraryName,
                $this->widgetDefinitionName
            )
        );
    }

    /**
     * Fetches the unique name of the library that defines this widget definition
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the unique name of the widget definition
     *
     * @return string
     */
    public function getWidgetDefinitionName()
    {
        return $this->widgetDefinitionName;
    }
}
