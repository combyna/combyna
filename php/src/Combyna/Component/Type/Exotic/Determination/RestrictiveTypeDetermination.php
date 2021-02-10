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
 * Class RestrictiveTypeDetermination
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RestrictiveTypeDetermination implements TypeDeterminationInterface
{
    /**
     * @var TypeInterface
     */
    private $destinationType;

    /**
     * @param TypeInterface $destinationType
     */
    public function __construct(TypeInterface $destinationType)
    {
        $this->destinationType = $destinationType;
    }

    /**
     * {@inheritdoc}
     */
    public function allowsType(TypeInterface $candidateType)
    {
        return $this->destinationType->allows($candidateType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByAnyType(AnyType $superType)
    {
        return $this->destinationType->isAllowedByAnyType($superType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByExoticType(ExoticType $otherType)
    {
        return $this->destinationType->isAllowedByExoticType($otherType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByMultipleType(MultipleType $otherType)
    {
        return $this->destinationType->isAllowedByMultipleType($otherType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticListType(StaticListType $otherType)
    {
        return $this->destinationType->isAllowedByStaticListType($otherType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticStructureType(StaticStructureType $otherType)
    {
        return $this->destinationType->isAllowedByStaticStructureType($otherType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByStaticType(StaticType $otherType)
    {
        return $this->destinationType->isAllowedByStaticType($otherType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByValuedType(ValuedType $otherType)
    {
        return $this->destinationType->isAllowedByValuedType($otherType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedByVoidType(VoidType $otherType)
    {
        return $this->destinationType->isAllowedByVoidType($otherType);
    }
}
