<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Entity\Evaluation;

use Combyna\Component\Entity\EntityInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Interface EntityEvaluationContextInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EntityEvaluationContextInterface extends EvaluationContextInterface
{
    /**
     * Fetches the entity this context is of
     *
     * @return EntityInterface
     */
    public function getEntity();
}
