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
 * Class ChildReferenceWidgetNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ChildReferenceWidgetNode extends AbstractActNode implements WidgetNodeInterface
{
    const TYPE = 'child-reference-widget';

    /**
     * @var string
     */
    private $childName;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var ExpressionNodeInterface|null
     */
    private $visibilityExpressionNode;

    /**
     * @param string $childName
     * @param ExpressionNodeInterface|null $visibilityExpressionNode
     * @param array $tags
     */
    public function __construct(
        $childName,
        ExpressionNodeInterface $visibilityExpressionNode = null,
        array $tags = []
    ) {
        $this->childName = $childName;
        $this->tags = $tags;
        $this->visibilityExpressionNode = $visibilityExpressionNode;
    }

    /**
     * Fetches the name of the compound widget child to reference
     *
     * @return string
     */
    public function getChildName()
    {
        return $this->childName;
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
        return 'child';
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        // FIXME
        // Check that the compound widget we are inside actually has a child with this name
//        $subValidationContext->assertCompoundWidgetHasChild($this->childName);

        if ($this->visibilityExpressionNode) {
            $this->visibilityExpressionNode->validate($subValidationContext);

            // Make sure the visibility expression always evaluates to a boolean
            $subValidationContext->assertResultType(
                $this->visibilityExpressionNode,
                new StaticType(BooleanExpression::class),
                'visibility'
            );
        }
    }
}
