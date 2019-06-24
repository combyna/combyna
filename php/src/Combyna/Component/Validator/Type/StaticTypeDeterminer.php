<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Type;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class StaticTypeDeterminer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticTypeDeterminer extends AbstractTypeDeterminer
{
    /**
     * @var string
     */
    private $staticClass;

    /**
     * @param string $staticClass
     */
    public function __construct($staticClass)
    {
        $this->staticClass = $staticClass;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        return new StaticType($this->staticClass, $validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
