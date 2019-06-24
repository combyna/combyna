<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Instruction;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\StaticStructureExpression;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Trigger\Instruction\InstructionInterface;
use LogicException;

/**
 * Class NavigateInstruction
 *
 * An instruction for only triggers to use that navigates to a different route
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NavigateInstruction implements InstructionInterface
{
    /**
     * @var ExpressionInterface
     */
    private $argumentStructureExpression;

    /**
     * @var ExpressionInterface
     */
    private $routeNameExpression;

    /**
     * @param ExpressionInterface $routeNameExpression
     * @param ExpressionInterface $argumentStructureExpression
     */
    public function __construct(
        ExpressionInterface $routeNameExpression,
        ExpressionInterface $argumentStructureExpression
    ) {
        $this->argumentStructureExpression = $argumentStructureExpression;
        $this->routeNameExpression = $routeNameExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function perform(
        EvaluationContextInterface $evaluationContext,
        ProgramStateInterface $programState,
        ProgramInterface $program
    ) {
        list($libraryName, $routeName) = explode(
            '.',
            $this->routeNameExpression->toStatic($evaluationContext)->toNative()
        );

        $argumentStructureStatic = $this->argumentStructureExpression->toStatic($evaluationContext);

        if (!$argumentStructureStatic instanceof StaticStructureExpression) {
            // This should have been caught by validation
            throw new LogicException(sprintf(
                'Expected a "%s" expression, got a "%s"',
                StaticStructureExpression::TYPE,
                $argumentStructureStatic->getType()
            ));
        }

        return $program->navigateTo(
            $programState,
            $libraryName,
            $routeName,
            $argumentStructureStatic->getAttributeStaticBag()
        );
    }
}
