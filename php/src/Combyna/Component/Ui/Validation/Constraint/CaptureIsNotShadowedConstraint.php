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
 * Class CaptureIsNotShadowedConstraint
 *
 * Ensures that a descendant does not attempt to define a capture with the same name as one defined by an ancestor.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureIsNotShadowedConstraint implements ConstraintInterface
{
    /**
     * @var string
     */
    private $captureName;

    /**
     * @param string $captureName
     */
    public function __construct($captureName)
    {
        $this->captureName = $captureName;
    }

    /**
     * Fetches the name of the capture being referenced
     *
     * @return string
     */
    public function getCaptureName()
    {
        return $this->captureName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
