<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\NothingExpression;
use Combyna\Component\Type\MultipleType;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Ui\Config\Act\ConditionalWidgetNode;
use Combyna\Component\Ui\Config\Act\OptionalWidgetNodeInterface;
use Combyna\Component\Ui\Config\Act\RepeaterWidgetNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;
use LogicException;

/**
 * Class CorrectCaptureTypeQuery
 *
 * Fetches the type a capture is expected to be defined with
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CorrectCaptureTypeQuery implements ResultTypeQueryInterface
{
    /**
     * @var string
     */
    private $captureName;

    /**
     * @var OptionalWidgetNodeInterface[]
     */
    private $optionalAncestorWidgetNodesFound = [];

    /**
     * @var ExpressionNodeInterface
     */
    private $setterExpressionNode;

    /**
     * @param string $captureName
     * @param ExpressionNodeInterface $setterExpressionNode Expression used by the setter widget to set the capture
     */
    public function __construct($captureName, ExpressionNodeInterface $setterExpressionNode)
    {
        $this->captureName = $captureName;
        $this->setterExpressionNode = $setterExpressionNode;
    }

    /**
     * Determines the correct type for the capture based on the result type
     * of its setter expression and any optional ancestors it has between
     * where it is set and where it is defined
     *
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function determineCorrectType(ValidationContextInterface $validationContext)
    {
        $type = $this->setterExpressionNode->getResultTypeDeterminer()->determine($validationContext);

        foreach ($this->optionalAncestorWidgetNodesFound as $index => $optionalAncestorWidgetNode) {
            if ($optionalAncestorWidgetNode instanceof RepeaterWidgetNode) {
                $type = new StaticListType($type);
            } elseif ($optionalAncestorWidgetNode instanceof ConditionalWidgetNode) {
                if ($index === count($this->optionalAncestorWidgetNodesFound) - 1) {
                    /*
                     * Special case - the first conditional between where a capture is defined
                     * and where it is set does not need to add `...|nothing` to the type,
                     * as in this scenario the capture will need to have a default expression
                     * defined which would be used when the capture was left unset
                     */
                    break;
                }

                $type = new MultipleType([$type, new StaticType(NothingExpression::class)]);
            } else {
                throw new LogicException('Unexpected widget node type');
            }
        }

        return $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return sprintf(
            'The correct, required type for the capture "%s"',
            $this->captureName
        );
    }

    /**
     * Fetches the name of the capture to fetch the correct type for
     *
     * @return string
     */
    public function getCaptureName()
    {
        return $this->captureName;
    }

    /**
     * Fetches the list of ancestor widgets found so far that are optional
     *
     * @return OptionalWidgetNodeInterface[]
     */
    public function getOptionalAncestorWidgetsFound()
    {
        return $this->optionalAncestorWidgetNodesFound;
    }

    /**
     * Fetches the expression used by the setter widget to set the value of the capture
     *
     * @return ExpressionNodeInterface
     */
    public function getSetterExpression()
    {
        return $this->setterExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }

    /**
     * Creates a new CorrectCaptureQuery with the provided additional ancestor widget node
     *
     * @param OptionalWidgetNodeInterface $ancestorWidgetNode
     * @return CorrectCaptureTypeQuery
     */
    public function withOptionalAncestor(OptionalWidgetNodeInterface $ancestorWidgetNode)
    {
        $newQuery = new self($this->captureName, $this->setterExpressionNode);
        $newQuery->optionalAncestorWidgetNodesFound = array_merge(
            $this->optionalAncestorWidgetNodesFound,
            [$ancestorWidgetNode]
        );

        return $newQuery;
    }
}
