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
 * Class CompoundWidgetDefinitionHasChildConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CompoundWidgetDefinitionHasChildConstraint implements ConstraintInterface
{
    /**
     * @var string
     */
    private $childName;

    /**
     * @param string $childName
     */
    public function __construct($childName)
    {
        $this->childName = $childName;
    }

    /**
     * Fetches the name of the child that must exist for the definition
     *
     * @return string
     */
    public function getChildName()
    {
        return $this->childName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
