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
use Combyna\Component\Entity\EntityStorageInterface;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Class EntityEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EntityEvaluationContext extends AbstractEvaluationContext
{
    /**
     * @var EntityInterface
     */
    private $entity;

    /**
     * @var EntityStorageInterface
     */
    private $entityStorage;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     * @param EntityInterface $entity
     * @param EntityStorageInterface $entityStorage
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EvaluationContextInterface $parentContext,
        EntityInterface $entity,
        EntityStorageInterface $entityStorage
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->entity = $entity;
        $this->entityStorage = $entityStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {

    }
}
