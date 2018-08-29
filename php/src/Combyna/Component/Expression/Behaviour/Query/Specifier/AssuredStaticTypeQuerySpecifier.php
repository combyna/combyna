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
 * Class AssuredStaticTypeQuerySpecifier
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredStaticTypeQuerySpecifier implements QuerySpecifierInterface
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
        return 'A query for the type of the assured static "' . $this->staticName . '" defined by a guard expression';
    }

    /**
     * Fetches the name of the assured static to query the type of
     *
     * @return string
     */
    public function getAssuredStaticName()
    {
        return $this->staticName;
    }
}
