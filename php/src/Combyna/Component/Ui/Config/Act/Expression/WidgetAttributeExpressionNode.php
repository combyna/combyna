<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act\Expression;

use Combyna\Component\Expression\Config\Act\AbstractExpressionNode;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Ui\Expression\WidgetAttributeExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class WidgetAttributeExpressionNode
 *
 * Fetches the value of an attribute specified for the compound widget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetAttributeExpressionNode extends AbstractExpressionNode
{
    const TYPE = WidgetAttributeExpression::TYPE;

    /**
     * @var string
     */
    private $attributeName;

    /**
     * @param string $attributeName
     */
    public function __construct($attributeName)
    {
        $this->attributeName = $attributeName;
    }

    /**
     * Fetches the name of the attribute to fetch
     *
     * @return string
     */
    public function getAttributeName()
    {
        return $this->attributeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
//        return $validationContext->getWidgetAttributeType($this->queryName);

        // FIXME!
        return new StaticType(TextExpression::class);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        // FIXME!
//        $subValidationContext->assertInsideCompoundWidgetDefinitionRootWidget();
//        $subValidationContext->assertCompoundWidgetHasAttribute($this->attributeName);
    }
}
