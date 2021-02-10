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
use Combyna\Component\Type\Exotic\Determination\RestrictiveTypeDetermination;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use LogicException;

/**
 * Class UnresolvedExoticTypeDeterminer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnresolvedExoticTypeDeterminer implements ExoticTypeDeterminerInterface
{
    const NAME = 'unresolved';

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param string $contextDescription
     * @param ValidationContextInterface $validationContext
     */
    public function __construct($contextDescription, ValidationContextInterface $validationContext)
    {
        $this->contextDescription = $contextDescription;
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
        throw new LogicException(
            sprintf(
                'Attempted to coerce native value for unresolved exotic type: %s',
                $this->contextDescription
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext)
    {
        throw new LogicException(
            sprintf(
                'Attempted to coerce static for unresolved exotic type: %s',
                $this->contextDescription
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function determine(TypeInterface $destinationType, TypeInterface $candidateType)
    {
        return new RestrictiveTypeDetermination(
            new UnresolvedType(
                $this->contextDescription,
                $this->validationContext
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
