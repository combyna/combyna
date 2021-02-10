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
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class StaticListTypeDeterminer
 *
 * Defines a type for a static list
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticListTypeDeterminer implements TypeDeterminerInterface
{
    /**
     * @var TypeDeterminerInterface
     */
    private $elementTypeDeterminer;

    /**
     * @param TypeDeterminerInterface $elementTypeDeterminer
     */
    public function __construct(TypeDeterminerInterface $elementTypeDeterminer)
    {
        $this->elementTypeDeterminer = $elementTypeDeterminer;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        return new StaticListType($this->elementTypeDeterminer->determine($validationContext));
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $this->elementTypeDeterminer->makesQuery($querySpecifier);
    }
}
