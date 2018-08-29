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
use Combyna\Component\Event\Behaviour\Query\Specifier\CurrentEventHasPayloadStaticQuerySpecifier;
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class CurrentEventHasPayloadStaticQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CurrentEventHasPayloadStaticQuery implements BooleanQueryInterface
{
    /**
     * @var string
     */
    private $staticName;

    /**
     * @param string $staticName
     */
    public function __construct($staticName)
    {
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
        return 'Whether the event payload has a static called "' . $this->staticName . '"';
    }

    /**
     * Fetches the name of the payload static to query the existence of
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
        return $querySpecifier instanceof CurrentEventHasPayloadStaticQuerySpecifier &&
            $querySpecifier->getPayloadStaticName() === $this->staticName;
    }
}
