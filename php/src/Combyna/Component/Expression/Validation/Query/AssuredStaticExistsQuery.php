<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Expression\Behaviour\Query\Specifier\AssuredStaticExistsQuerySpecifier;
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class AssuredStaticExistsQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredStaticExistsQuery implements BooleanQueryInterface
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
     * Fetches the name of the assured static to query the existence of
     *
     * @return string
     */
    public function getAssuredStaticName()
    {
        return $this->staticName;
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
        return 'Whether an ancestor expression defines the assured static "' . $this->staticName . '"';
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $querySpecifier instanceof AssuredStaticExistsQuerySpecifier &&
            $querySpecifier->getStaticName() === $this->staticName;
    }
}
