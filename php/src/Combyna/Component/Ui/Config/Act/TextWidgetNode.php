<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act;

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class TextWidgetNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextWidgetNode extends AbstractActNode implements WidgetNodeInterface
{
    const TYPE = 'text-widget';

    /**
     * @var array
     */
    private $tags;

    /**
     * @var ExpressionNodeInterface
     */
    private $textExpressionNode;

    /**
     * @var ExpressionNodeInterface|null
     */
    private $visibilityExpressionNode;

    /**
     * @param ExpressionNodeInterface $textExpressionNode
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     * @param array $tags
     */
    public function __construct(
        ExpressionNodeInterface $textExpressionNode,
        ExpressionNodeInterface $visibilityExpressionNode = null,
        array $tags = []
    ) {
        $this->tags = $tags;
        $this->textExpressionNode = $textExpressionNode;
        $this->visibilityExpressionNode = $visibilityExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildWidgets()
    {
        return []; // Text widgets cannot have any children
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return LibraryInterface::CORE;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Fetches the expression to be evaluated for the text content of this widget
     *
     * @return ExpressionNodeInterface
     */
    public function getTextExpression()
    {
        return $this->textExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibilityExpression()
    {
        return $this->visibilityExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->textExpressionNode->validate($subValidationContext);

        // Validate ourself
        if ($this->visibilityExpressionNode) {
            $this->visibilityExpressionNode->validate($subValidationContext);

            $subValidationContext->assertResultType(
                $this->visibilityExpressionNode,
                new StaticType(BooleanExpression::class),
                'visibility'
            );
        }
    }
}
