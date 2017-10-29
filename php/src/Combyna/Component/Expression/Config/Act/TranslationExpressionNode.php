<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Expression\TranslationExpression;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

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
    public function getResultType(ValidationContextInterface $validationContext)
    {
        return new StaticType(TextExpression::class);
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

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        if ($this->translationKey === '') {
            $subValidationContext->addGenericViolation('Translation key cannot be empty');
        }

        if ($this->argumentExpressionBag !== null) {
            // TODO: Validate that parameters all always evaluate to texts

            $this->argumentExpressionBag->validate($subValidationContext);
        }
    }
}
