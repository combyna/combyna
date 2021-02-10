<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Behaviour\Query\Specifier;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;

/**
 * Class AssuredStaticExistsQuerySpecifier
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredStaticExistsQuerySpecifier implements QuerySpecifierInterface
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
        return 'A query for whether an ancestor expression defines the assured static "' . $this->staticName . '"';
    }

    /**
     * Fetches the name of the assured static to query the existence of
     *
     * @return string
     */
    public function getStaticName()
    {
        return $this->staticName;
    }
}
