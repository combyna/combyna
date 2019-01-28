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
 * Class CaptureHasDefaultIfOptionalConstraint
 *
 * Ensures that if a capture is optional (because between the widget that defines it
 * and the widget that sets it, there is a repeater or conditional widget)
 * then the capture has a default expression defined.
 * This is because when the conditional or repeater widget does not show the setter widget,
 * it will not set the value of the capture, so it will fall back to evaluating the default.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureHasDefaultIfOptionalConstraint implements ConstraintInterface
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
