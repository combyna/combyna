<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\StaticInterface;
use LogicException;

/**
 * Class FixedStaticDefinition
 *
 * Defines the name, type and default static value for a static in a bag
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedStaticDefinition
{
    /**
     * @var ExpressionInterface|null
     */
    private $defaultExpression;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     * @param ExpressionInterface|null $defaultExpression
     */
    public function __construct(
        $name,
        ExpressionInterface $defaultExpression = null
    ) {
        $this->defaultExpression = $defaultExpression;
        $this->name = $name;
    }

    /**
     * Fetches the default value for this static, if configured
     *
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticInterface
     * @throws LogicException when no default static has been configured
     */
    public function getDefaultStatic(EvaluationContextInterface $evaluationContext)
    {
        if (!$this->defaultExpression) {
            throw new LogicException(
                'No default expression has been configured for parameter "' . $this->name . '"'
            );
        }

        return $this->defaultExpression->toStatic($evaluationContext);
    }

    /**
     * Fetches the name of the definition
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Determines whether this static must be defined in the bag or not
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->defaultExpression === null;
    }
}
