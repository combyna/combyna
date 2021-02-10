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
use Combyna\Component\Validator\Query\ActNodeQueryInterface;

/**
 * Class AssuranceNodeQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuranceNodeQuery implements ActNodeQueryInterface
{
    /**
     * @var string
     */
    private $assuredStaticName;

    /**
     * @param string $assuredStaticName
     */
    public function __construct($assuredStaticName)
    {
        $this->assuredStaticName = $assuredStaticName;
    }

    /**
     * Fetches the name of the assured static to fetch the assurance of
     *
     * @return string
     */
    public function getAssuredStaticName()
    {
        return $this->assuredStaticName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'The ACT node of the assurance for the assured static "' . $this->assuredStaticName . '"';
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
