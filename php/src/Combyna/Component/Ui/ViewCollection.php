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

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use InvalidArgumentException;

/**
 * Class ViewCollection
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewCollection implements ViewCollectionInterface
{
    /**
     * @var ViewInterface[]
     */
    private $views;

    /**
     * @param ViewInterface[] $views
     */
    public function __construct(array $views)
    {
        $viewsByName = [];

        // Ensure views are indexed by name
        foreach ($views as $view) {
            $viewsByName[$view->getName()] = $view;
        }

        $this->views = $viewsByName;
    }

    /**
     * {@inheritdoc}
     */
    public function renderView(
        $viewName,
        StaticBagInterface $viewAttributeStaticBag,
        EvaluationContextInterface $rootEvaluationContext
    ) {
        if (!array_key_exists($viewName, $this->views)) {
            throw new InvalidArgumentException('No view with name "' . $viewName . '" exists');
        }

        return $this->views[$viewName]->render($viewAttributeStaticBag, $rootEvaluationContext);
    }
}
