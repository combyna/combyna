<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Exotic\Determination;

use Combyna\Component\Type\AnyType;
use Combyna\Component\Type\ExoticType;
use Combyna\Component\Type\MultipleType;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticStructureType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\ValuedType;
use Combyna\Component\Type\VoidType;

/**
 * Class UnrestrictiveTypeDetermination
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnrestrictiveTypeDetermination implements TypeDeterminationInterface
{
    /**
     * {@inheritdoc}
     */
    public function allowsType(TypeInterface $candidateType)
    {
        return true; // Allow everything
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByAnyType(AnyType $superType)
    {
        return true; // Allow an unrestricted type to go anywhere
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByExoticType(ExoticType $otherType)
    {
        return true; // Allow an unrestricted type to go anywhere
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByMultipleType(MultipleType $otherType)
    {
        return true; // Allow an unrestricted type to go anywhere
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticListType(StaticListType $otherType)
    {
        return true; // Allow an unrestricted type to go anywhere
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticStructureType(StaticStructureType $otherType)
    {
        return true; // Allow an unrestricted type to go anywhere
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticType(StaticType $otherType)
    {
        return true; // Allow an unrestricted type to go anywhere
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByValuedType(ValuedType $otherType)
    {
        return true; // Allow an unrestricted type to go anywhere
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByVoidType(VoidType $otherType)
    {
        return true; // Allow an unrestricted type to go anywhere
    }
}
