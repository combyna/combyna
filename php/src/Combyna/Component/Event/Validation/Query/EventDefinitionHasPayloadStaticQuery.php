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
 * Class EventDefinitionHasPayloadStaticQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionHasPayloadStaticQuery implements BooleanQueryInterface
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
            'Whether the event definition "%s.%s" defines the payload static "%s"',
            $this->libraryName,
            $this->eventName,
            $this->staticName
        );
    }

    /**
     * Fetches the name of the event that should define the static in its payload
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * Fetches the name of the library that defines the event
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the name of the static
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
