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

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;

/**
 * Class SignalDefinitionHasPayloadStaticConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionHasPayloadStaticConstraint implements ConstraintInterface
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
     * @var string
     */
    private $staticName;

    /**
     * @param string $libraryName
     * @param string $signalName
     * @param string $staticName
     */
    public function __construct($libraryName, $signalName, $staticName)
    {
        $this->libraryName = $libraryName;
        $this->signalName = $signalName;
        $this->staticName = $staticName;
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
     * Fetches the name of the payload static
     *
     * @return string
     */
    public function getPayloadStaticName()
    {
        return $this->staticName;
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

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
