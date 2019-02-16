<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Behaviour\Query\Specifier;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;

/**
 * Class SourceSignalHasPayloadStaticQuerySpecifier
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SourceSignalHasPayloadStaticQuerySpecifier implements QuerySpecifierInterface
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
    public function getDescription()
    {
        return 'A query for whether the signal payload has a static called "' . $this->staticName . '"';
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
}
