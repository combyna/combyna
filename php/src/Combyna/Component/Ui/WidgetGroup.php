<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui;

use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use InvalidArgumentException;

/**
 * Class WidgetGroup
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetGroup implements WidgetInterface
{
    const DEFINITION = 'widget-group';

    /**
     * @var WidgetInterface[]
     */
    private $childWidgets = [];

    /**
     * @var ExpressionInterface|null
     */
    private $visibilityExpression;

    /**
     * @param ExpressionInterface|null $visibilityExpression
     */
    public function __construct(
        ExpressionInterface $visibilityExpression = null
    ) {
        $this->visibilityExpression = $visibilityExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild($childName, WidgetInterface $childWidget)
    {
        $this->childWidgets[$childName] = $childWidget;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionLibraryName()
    {
        return self::LIBRARY;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionName()
    {
        return self::DEFINITION;
    }
}
