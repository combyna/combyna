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
 * Class CaptureHasNoDefaultIfNotOptionalConstraint
 *
 * Ensures that if a capture is not optional (because between the widget that defines it
 * and the widget that sets it, there is no repeater nor conditional widget)
 * then the capture has no default expression defined.
 * This is because the setter widget will always be able to set the value of the capture,
 * so the default expression would never be used.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureHasNoDefaultIfNotOptionalConstraint implements ConstraintInterface
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
