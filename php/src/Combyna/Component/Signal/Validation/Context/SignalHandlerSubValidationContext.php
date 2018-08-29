<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Validation\Context;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Signal\Config\Act\SignalHandlerNode;
use Combyna\Component\Signal\Validation\Query\CurrentSignalHasPayloadStaticQuery;
use Combyna\Component\Signal\Validation\Query\CurrentSignalPayloadStaticTypeQuery;
use Combyna\Component\Signal\Validation\Query\InsideSignalHandlerQuery;
use Combyna\Component\Signal\Validation\Query\SignalDefinitionHasPayloadStaticQuery;
use Combyna\Component\Signal\Validation\Query\SignalDefinitionPayloadStaticTypeQuery;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class SignalHandlerSubValidationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalHandlerSubValidationContext implements SignalHandlerSubValidationContextInterface
{
    /**
     * @var SubValidationContextInterface
     */
    private $parentContext;

    /**
     * @var SignalHandlerNode
     */
    private $signalHandlerNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $signalHandlerNodeBehaviourSpec;

    /**
     * @param SubValidationContextInterface $parentContext
     * @param SignalHandlerNode $signalHandlerNode
     * @param BehaviourSpecInterface $signalHandlerNodeBehaviourSpec
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        SignalHandlerNode $signalHandlerNode,
        BehaviourSpecInterface $signalHandlerNodeBehaviourSpec
    ) {
        $this->parentContext = $parentContext;
        $this->signalHandlerNode = $signalHandlerNode;
        $this->signalHandlerNodeBehaviourSpec = $signalHandlerNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getActNode()
    {
        return $this->signalHandlerNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpec()
    {
        return $this->signalHandlerNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentContext()
    {
        return $this->parentContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        $path = $this->parentContext->getPath();

        if ($path !== '') {
            $path .= '.';
        }

        $path .= sprintf(
            '[signal:%s.%s handler]',
            $this->signalHandlerNode->getSignalDefinitionReference()->getLibraryName(),
            $this->signalHandlerNode->getSignalDefinitionReference()->getSignalName()
        );

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryClassToQueryCallableMap()
    {
        return [
            InsideSignalHandlerQuery::class => [$this, 'queryForInsideSignalHandler'],
            CurrentSignalHasPayloadStaticQuery::class => [$this, 'queryForSignalPayloadStaticExistence'],
            CurrentSignalPayloadStaticTypeQuery::class => [$this, 'queryForSignalPayloadStaticType']
        ];
    }

    /**
     * Determines whether or not we are inside a signal handler
     *
     * @return bool
     */
    public function queryForInsideSignalHandler()
    {
        return true;
    }

    /**
     * Determines whether the signal being handled defines the specified payload static
     *
     * @param CurrentSignalHasPayloadStaticQuery $query
     * @param ValidationContextInterface $validationContext
     * @return bool
     */
    public function queryForSignalPayloadStaticExistence(
        CurrentSignalHasPayloadStaticQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $signalDefinitionReferenceNode = $this->signalHandlerNode->getSignalDefinitionReference();

        return $validationContext->queryForBoolean(
            new SignalDefinitionHasPayloadStaticQuery(
                $signalDefinitionReferenceNode->getLibraryName(),
                $signalDefinitionReferenceNode->getSignalName(),
                $query->getPayloadStaticName()
            ),
            $this->signalHandlerNode
        );
    }

    /**
     * Fetches the type of the specified payload static for the current handler's signal
     *
     * @param CurrentSignalPayloadStaticTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface|null
     */
    public function queryForSignalPayloadStaticType(
        CurrentSignalPayloadStaticTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $signalDefinitionReferenceNode = $this->signalHandlerNode->getSignalDefinitionReference();

        return $validationContext->queryForResultType(
            new SignalDefinitionPayloadStaticTypeQuery(
                $signalDefinitionReferenceNode->getLibraryName(),
                $signalDefinitionReferenceNode->getSignalName(),
                $query->getPayloadStaticName()
            ),
            $this->signalHandlerNode
        );
    }
}
