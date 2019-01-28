<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Validation\Constraint;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;

/**
 * Class EventDefinitionExistsConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionExistsConstraint implements ConstraintInterface
{
    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $eventName;

    /**
     * @param string $libraryName
     * @param string $eventName
     */
    public function __construct($libraryName, $eventName)
    {
        $this->libraryName = $libraryName;
        $this->eventName = $eventName;
    }

    /**
     * Fetches the name of the library that should define the event
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the name of the event
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
