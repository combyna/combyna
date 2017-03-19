<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Entity;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticInterface;

/**
 * Interface QueryMethodInterface
 *
 * Defines an entrypoint for expressions outside the entity to interrogate it for information
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface QueryMethodInterface
{
    /**
     * Makes this query against the entity, evaluating the expression it defines.
     * This expression is able to access the private internal state of the entity as required
     *
     * @param StaticBagInterface $argumentStaticBag
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticInterface
     */
    public function make(
        StaticBagInterface $argumentStaticBag,
        EvaluationContextInterface $evaluationContext
    );
}
