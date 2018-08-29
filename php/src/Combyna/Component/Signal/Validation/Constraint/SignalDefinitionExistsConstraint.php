<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Validation\Constraint;

use Combyna\Component\Validator\Constraint\ConstraintInterface;

/**
 * Class SignalDefinitionExistsConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionExistsConstraint implements ConstraintInterface
{
    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $signalName;

    /**
     * @param string $libraryName
     * @param string $signalName
     */
    public function __construct($libraryName, $signalName)
    {
        $this->libraryName = $libraryName;
        $this->signalName = $signalName;
    }

    /**
     * Fetches the name of the library that should define the signal
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the name of the signal
     *
     * @return string
     */
    public function getSignalName()
    {
        return $this->signalName;
    }
}
