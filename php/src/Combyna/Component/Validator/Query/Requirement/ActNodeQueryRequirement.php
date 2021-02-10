<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Query\Requirement;

use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Validator\Context\RootValidationContextInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Query\ActNodeQueryInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Class ActNodeQueryRequirement
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ActNodeQueryRequirement implements QueryRequirementInterface
{
    /**
     * @var ActNodeQueryInterface
     */
    private $query;

    /**
     * @var RootValidationContextInterface
     */
    private $rootValidationContext;

    /**
     * @var SubValidationContextInterface
     */
    private $subValidationContext;

    /**
     * @var ValidationContextInterface
     */
    private $validationContext;

    /**
     * @param ActNodeQueryInterface $query
     * @param RootValidationContextInterface $rootValidationContext
     * @param SubValidationContextInterface $subValidationContext
     * @param ValidationContextInterface $validationContext
     */
    public function __construct(
        ActNodeQueryInterface $query,
        RootValidationContextInterface $rootValidationContext,
        SubValidationContextInterface $subValidationContext,
        ValidationContextInterface $validationContext
    ) {
        $this->query = $query; // Not used yet
        $this->rootValidationContext = $rootValidationContext;
        $this->subValidationContext = $subValidationContext;
        $this->validationContext = $validationContext;
    }

    /**
     * {@inheritdoc}
     */
    public function adoptDynamicActNode(DynamicActNodeInterface $actNode)
    {
        $this->rootValidationContext->adoptDynamicActNode(
            $actNode,
            $this->subValidationContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function determineType(TypeDeterminerInterface $typeDeterminer)
    {
        return $typeDeterminer->determine($this->validationContext);
    }
}
