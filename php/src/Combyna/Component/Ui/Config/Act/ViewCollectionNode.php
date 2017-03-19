<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act;

use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ViewCollectionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewCollectionNode extends AbstractActNode
{
    const TYPE = 'view-collection';

    /**
     * @var ViewNode[]
     */
    private $viewNodes;

    /**
     * @param ViewNode[] $viewNodes
     */
    public function __construct(array $viewNodes)
    {
        $this->viewNodes = $viewNodes;
    }

    /**
     * Fetches all views in this collection
     *
     * @return ViewNode[]
     */
    public function getViews()
    {
        return $this->viewNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        foreach ($this->viewNodes as $viewNode) {
            $viewNode->validate($subValidationContext);
        }
    }
}

