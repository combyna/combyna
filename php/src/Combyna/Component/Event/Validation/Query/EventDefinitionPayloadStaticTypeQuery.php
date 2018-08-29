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
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;

/**
 * Class EventDefinitionPayloadStaticTypeQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionPayloadStaticTypeQuery implements ResultTypeQueryInterface
{
    /**
     * @var string
     */
    private $eventName;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $staticName;

    /**
     * @param string $libraryName
     * @param string $eventName
     * @param string $staticName
     */
    public function __construct($libraryName, $eventName, $staticName)
    {
        $this->eventName = $eventName;
        $this->libraryName = $libraryName;
        $this->staticName = $staticName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return sprintf(
            'The type of the payload static "%s" for event "%s.%s"',
            $this->staticName,
            $this->libraryName,
            $this->eventName
        );
    }

    /**
     * Fetches the name of the library that defines the event
     *
     * @return string
     */
    public function getEventLibraryName()
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
     * Fetches the name of the payload static to query the result type of
     *
     * @return string
     */
    public function getPayloadStaticName()
    {
        return $this->staticName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
