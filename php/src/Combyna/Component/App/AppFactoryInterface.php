<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Ui\ViewCollectionInterface;

/**
 * Interface AppFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AppFactoryInterface
{
    /**
     * Creates a new app
     *
     * @param EvaluationContextInterface $rootEvaluationContext
     * @param ViewCollectionInterface $viewCollection
     * @return AppInterface
     */
    public function create(
        EvaluationContextInterface $rootEvaluationContext,
        ViewCollectionInterface $viewCollection
    );
}
