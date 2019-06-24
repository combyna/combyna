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
use Combyna\Component\Expression\StaticValueInterface;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\ValuedType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class StaticValuedTypeDeterminer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticValuedTypeDeterminer extends AbstractTypeDeterminer
{
    /**
     * @var string
     */
    private $staticClass;

    /**
     * @var StaticValueInterface
     */
    private $staticValue;

    /**
     * @param string $staticClass
     * @param StaticValueInterface $staticValue
     */
    public function __construct($staticClass, StaticValueInterface $staticValue)
    {
        $this->staticClass = $staticClass;
        $this->staticValue = $staticValue;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        return new ValuedType(
            new StaticType($this->staticClass, $validationContext),
            $this->staticValue,
            $validationContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
