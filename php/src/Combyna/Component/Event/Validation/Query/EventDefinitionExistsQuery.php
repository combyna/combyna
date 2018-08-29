<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class EventDefinitionExistsQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionExistsQuery implements BooleanQueryInterface
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
            'Whether the event definition "%s.%s" exists',
            $this->libraryName,
            $this->eventName
        );
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
     * Fetches the name of the event that should be defined within its library
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
