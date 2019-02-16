<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class SignalDefinitionHasPayloadStaticQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionHasPayloadStaticQuery implements BooleanQueryInterface
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
            'Whether the signal "%s.%s" defines the payload static "%s"',
            $this->libraryName,
            $this->signalName,
            $this->staticName
        );
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
     * Fetches the name of the library that defines the signal
     *
     * @return string
     */
    public function getSignalLibraryName()
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

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
