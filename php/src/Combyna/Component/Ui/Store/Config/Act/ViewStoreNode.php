<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Act;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Config\Act\DynamicContainerNode;
use Combyna\Component\Signal\Config\Act\SignalHandlerNode;
use Combyna\Component\Store\Config\Act\QueryNode;
use Combyna\Component\Store\Config\Act\QueryNodeInterface;
use Combyna\Component\Store\Config\Act\UnknownQueryNode;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Ui\Store\Validation\Context\Specifier\ViewStoreContextSpecifier;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;

/**
 * Class ViewStoreNode
 *
 * Defines a store with commands, queries and signal handlers for a view
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreNode extends AbstractActNode
{
    const TYPE = 'view-store';

    /**
     * @var DynamicContainerNode
     */
    private $dynamicContainerNode;

    /**
     * @var QueryNode[]
     */
    private $queryNodes;

    /**
     * @var SignalHandlerNode[]
     */
    private $signalHandlerNodes;

    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $slotBagModelNode;

    /**
     * @param FixedStaticBagModelNodeInterface $slotBagModelNode
     * @param QueryNode[] $queryNodes
     * @param SignalHandlerNode[] $signalHandlerNodes
     */
    public function __construct(
        FixedStaticBagModelNodeInterface $slotBagModelNode,
        array $queryNodes,
        array $signalHandlerNodes
    ) {
        $this->dynamicContainerNode = new DynamicContainerNode();
        $this->queryNodes = $queryNodes;
        $this->signalHandlerNodes = $signalHandlerNodes;
        $this->slotBagModelNode = $slotBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->dynamicContainerNode);
        $specBuilder->addChildNode($this->slotBagModelNode);

        $specBuilder->addSubSpec(function (BehaviourSpecBuilderInterface $subSpecBuilder) {
            // Queries and signal handlers can access the slots, but not vice versa
            $subSpecBuilder->defineValidationContext(new ViewStoreContextSpecifier());

            foreach ($this->queryNodes as $queryNode) {
                $subSpecBuilder->addChildNode($queryNode);
            }

            foreach ($this->signalHandlerNodes as $signalHandlerNode) {
                $subSpecBuilder->addChildNode($signalHandlerNode);
            }
        });
    }

    /**
     * Fetches the queries this store defines
     *
     * @return QueryNode[]
     */
    public function getQueries()
    {
        return $this->queryNodes;
    }

    /**
     * Fetches a query this store defines by its name
     *
     * @param string $name
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     * @return QueryNodeInterface
     */
    public function getQueryByName($name, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        return array_key_exists($name, $this->queryNodes) ?
            $this->queryNodes[$name] :
            new UnknownQueryNode($name, $dynamicActNodeAdopter);
    }

    /**
     * Fetches the signal handlers this store has
     *
     * @return SignalHandlerNode[]
     */
    public function getSignalHandlers()
    {
        return $this->signalHandlerNodes;
    }

    /**
     * Fetches the static bag model for slots in this store
     *
     * @return FixedStaticBagModelNodeInterface
     */
    public function getSlotBagModel()
    {
        return $this->slotBagModelNode;
    }

    /**
     * Fetches the static type of the specified slot, if it exists
     *
     * @param string $slotName
     * @return TypeInterface|null
     */
    public function getSlotStaticType($slotName)
    {
        $slotStaticDefinition = $this->slotBagModelNode->getStaticDefinitionByName(
            $slotName,
            $this->dynamicContainerNode
        );

        if ($slotStaticDefinition === null) {
            return null;
        }

        return $this->dynamicContainerNode->determineType($slotStaticDefinition->getStaticTypeDeterminer());
    }
}
