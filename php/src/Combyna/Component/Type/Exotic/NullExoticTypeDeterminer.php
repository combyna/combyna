<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\Exotic;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Type\AnyType;
use Combyna\Component\Type\Exotic\Determination\UnrestrictiveTypeDetermination;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class NullExoticTypeDeterminer
 *
 * Used by the NullValidationContext whenever an attempt is made to create
 * an Exotic type determiner at runtime (where the behaviour spec tree etc. are not loaded)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NullExoticTypeDeterminer implements ExoticTypeDeterminerInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param ValidationContextInterface $validationContext
     * @param string $name
     */
    public function __construct(ValidationContextInterface $validationContext, $name)
    {
        $this->name = $name;
        $this->validationContext = $validationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function coerceNative(
        $nativeValue,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        BagFactoryInterface $bagFactory,
        EvaluationContextInterface $evaluationContext
    ) {
        return (new AnyType($this->validationContext))->coerceNative(
            $nativeValue,
            $staticExpressionFactory,
            $bagFactory,
            $evaluationContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext)
    {
        return $static;
    }

    /**
     * {@inheritdoc}
     */
    public function determine(TypeInterface $destinationType, TypeInterface $candidateType)
    {
        return new UnrestrictiveTypeDetermination();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
