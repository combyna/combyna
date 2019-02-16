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
use Combyna\Component\Signal\Behaviour\Query\Specifier\SourceSignalHasPayloadStaticQuerySpecifier;
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class SourceSignalHasPayloadStaticQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SourceSignalHasPayloadStaticQuery implements BooleanQueryInterface
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
        return 'Whether the signal payload has a static called "' . $this->staticName . '"';
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
        return $querySpecifier instanceof SourceSignalHasPayloadStaticQuerySpecifier &&
            $querySpecifier->getPayloadStaticName() === $this->staticName;
    }
}
