<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Expression\TranslationExpression;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Type\StaticTypeDeterminer;

/**
 * Class TranslationExpressionNode
 *
 * Contains a list of expressions
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TranslationExpressionNode extends AbstractExpressionNode
{
    const TYPE = TranslationExpression::TYPE;

    /**
     * @var ExpressionBagNode|null
     */
    private $argumentExpressionBag;

    /**
     * @var string
     */
    private $translationKey;

    /**
     * @param string $translationKey
     * @param ExpressionBagNode|null $argumentExpressionBag
     */
    public function __construct($translationKey, ExpressionBagNode $argumentExpressionBag = null)
    {
        $this->argumentExpressionBag = $argumentExpressionBag;
        $this->translationKey = $translationKey;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        if ($this->translationKey === '') {
            $specBuilder->addConstraint(new KnownFailureConstraint('Translation key cannot be empty'));
        }

        if ($this->argumentExpressionBag !== null) {
            // Validate that parameters all always evaluate to texts
            foreach ($this->argumentExpressionBag->getExpressions() as $parameterName => $argumentExpressionNode) {
                $specBuilder->addConstraint(
                    new ResultTypeConstraint(
                        $argumentExpressionNode,
                        new StaticTypeDeterminer(TextExpression::class),
                        'parameter "' . $parameterName . '"'
                    )
                );
            }

            $specBuilder->addChildNode($this->argumentExpressionBag);
        }
    }

    /**
     * Fetches the bag of expressions for any parameters of the message, if set
     *
     * @return ExpressionBagNode|null
     */
    public function getArgumentExpressionBag()
    {
        return $this->argumentExpressionBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new StaticTypeDeterminer(TextExpression::class);
    }

    /**
     * Fetches the key of the message to be translated
     *
     * @return string
     */
    public function getTranslationKey()
    {
        return $this->translationKey;
    }
}
