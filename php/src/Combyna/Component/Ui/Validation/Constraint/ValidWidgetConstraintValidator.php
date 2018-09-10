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

use Combyna\Component\Ui\Config\Act\WidgetDefinitionNodeInterface;
use Combyna\Component\Ui\Validation\Query\WidgetDefinitionNodeQuery;
use Combyna\Component\Validator\Constraint\ConstraintValidatorInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use LogicException;

/**
 * Class ValidWidgetConstraintValidator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidWidgetConstraintValidator implements ConstraintValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConstraintClassToValidatorCallableMap()
    {
        return [
            ValidWidgetConstraint::class => [$this, 'validate']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPassClasses()
    {
        return []; // No passes needed
    }

    /**
     * Validates this constraint in the given validation context. If the constraint is not met,
     * one or more violations will be added to the context to make the validation fail
     *
     * @param ValidWidgetConstraint $constraint
     * @param ValidationContextInterface $validationContext
     */
    public function validate(
        ValidWidgetConstraint $constraint,
        ValidationContextInterface $validationContext
    ) {
        // May return an UnknownWidgetDefinitionNode or UnknownLibraryForWidgetDefinitionNode if invalid -
        // those nodes will not add any violations, it is expected that a WidgetDefinitionExistsConstraint
        // will be used in tandem
        $widgetDefinitionNode = $validationContext->queryForActNode(
            new WidgetDefinitionNodeQuery(
                $constraint->getWidgetDefinitionLibraryName(),
                $constraint->getWidgetDefinitionName()
            ),
            $validationContext->getCurrentActNode()
        );

        if (!$widgetDefinitionNode instanceof WidgetDefinitionNodeInterface) {
            throw new LogicException(sprintf(
                'Expected a widget definition node, got "%s"',
                get_class($widgetDefinitionNode)
            ));
        }

        $widgetDefinitionNode->validateWidget(
            $validationContext,
            $constraint->getAttributeExpressionBag(),
            $constraint->getChildWidgets()
        );
    }
}
