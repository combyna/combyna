<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Validation\Constraint;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Class ResolvableTypeConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ResolvableTypeConstraint implements ConstraintInterface
{
    /**
     * @var TypeDeterminerInterface
     */
    private $typeDeterminer;

    /**
     * @param TypeDeterminerInterface $typeDeterminer
     */
    public function __construct(TypeDeterminerInterface $typeDeterminer)
    {
        $this->typeDeterminer = $typeDeterminer;
    }

    /**
     * Fetches the type determiner
     *
     * @return TypeDeterminerInterface
     */
    public function getTypeDeterminer()
    {
        return $this->typeDeterminer;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
