<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Signal\SignalDefinitionReferenceInterface;
use Combyna\Component\Ui\Store\Instruction\ViewStoreInstructionListInterface;
use Combyna\Component\Ui\Store\Query\StoreQueryCollectionInterface;
use Combyna\Component\Ui\Store\Query\StoreQueryInterface;
use Combyna\Component\Ui\Store\Signal\ViewStoreSignalHandlerInterface;

/**
 * Interface UiStoreFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface UiStoreFactoryInterface
{
    /**
     * Creates a NullViewStore
     *
     * @param string $viewName
     * @return NullViewStoreInterface
     */
    public function createNullViewStore($viewName);

    /**
     * Creates a new StoreQuery
     *
     * @param string $name
     * @param FixedStaticBagModelInterface $parameterStaticBagModel
     * @param ExpressionInterface $expression
     * @return StoreQueryInterface
     */
    public function createQuery(
        $name,
        FixedStaticBagModelInterface $parameterStaticBagModel,
        ExpressionInterface $expression
    );

    /**
     * Creates a new StoreQueryCollection
     *
     * @param StoreQueryInterface[] $queries
     * @return StoreQueryCollectionInterface
     */
    public function createQueryCollection(array $queries);

    /**
     * Creates a new ViewStore
     *
     * @param string $viewName
     * @param FixedStaticBagModelInterface $slotStaticBagModel
     * @param ViewStoreSignalHandlerInterface[] $signalHandlers
     * @param StoreCommandInterface[] $commands
     * @param StoreQueryCollectionInterface $queryCollection
     * @return ViewStoreInterface
     */
    public function createViewStore(
        $viewName,
        FixedStaticBagModelInterface $slotStaticBagModel,
        array $signalHandlers,
        array $commands,
        StoreQueryCollectionInterface $queryCollection
    );

    /**
     * Creates a new ViewStoreSignalHandler
     *
     * @param SignalDefinitionReferenceInterface $signalDefinitionReference
     * @param ViewStoreInstructionListInterface $instructionList
     * @param ExpressionInterface|null $guardExpression
     * @return ViewStoreSignalHandlerInterface
     */
    public function createViewStoreSignalHandler(
        SignalDefinitionReferenceInterface $signalDefinitionReference,
        ViewStoreInstructionListInterface $instructionList,
        ExpressionInterface $guardExpression = null
    );
}
