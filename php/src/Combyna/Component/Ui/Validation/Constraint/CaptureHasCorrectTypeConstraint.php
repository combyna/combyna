<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Constraint;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;

/**
 * Class CaptureHasCorrectTypeConstraint
 *
 * Ensures that the type of a capture allows the result type of its setter expression.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureHasCorrectTypeConstraint implements ConstraintInterface
{
    /**
     * @var string
     */
    private $captureName;

    /**
     * @var ExpressionNodeInterface
     */
    private $setterExpressionNode;

    /**
     * @param string $captureName
     * @param ExpressionNodeInterface $setterExpressionNode
     */
    public function __construct($captureName, ExpressionNodeInterface $setterExpressionNode)
    {
        $this->captureName = $captureName;
        $this->setterExpressionNode = $setterExpressionNode;
    }

    /**
     * Fetches the name of the capture being referenced
     *
     * @return string
     */
    public function getCaptureName()
    {
        return $this->captureName;
    }

    /**
     * Fetches the expression used to set the value of the capture
     *
     * @return ExpressionNodeInterface
     */
    public function getSetterExpressionNode()
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
}
