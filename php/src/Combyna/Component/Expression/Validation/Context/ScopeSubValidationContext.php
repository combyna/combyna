<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Validation\Context;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Expression\Validation\Query\VariableExistsQuery;
use Combyna\Component\Expression\Validation\Query\VariableTypeQuery;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Class ScopeSubValidationContext
 *
 * Represents a scope within which a group of variables are defined,
 * eg. the item and index variables inside a MapExpression
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ScopeSubValidationContext implements ScopeSubValidationContextInterface
{
    /**
     * @var ActNodeInterface
     */
    private $actNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $behaviourSpec;

    /**
     * @var SubValidationContextInterface
     */
    private $parentContext;

    /**
     * @var ActNodeInterface
     */
    private $subjectNode;

    /**
     * @var TypeDeterminerInterface[]
     */
    private $variableTypeDeterminers;

    /**
     * @param SubValidationContextInterface $parentContext
     * @param TypeDeterminerInterface[] $variableTypeDeterminers
     * @param ActNodeInterface $actNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @param ActNodeInterface $subjectNode
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        array $variableTypeDeterminers,
        ActNodeInterface $actNode,
        BehaviourSpecInterface $behaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        $this->actNode = $actNode;
        $this->behaviourSpec = $behaviourSpec;
        $this->parentContext = $parentContext;
        $this->subjectNode = $subjectNode;
        $this->variableTypeDeterminers = $variableTypeDeterminers;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
    {
        return $this->actNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpec()
    {
        return $this->behaviourSpec;
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

        $path .= '[scope]';

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryClassToQueryCallableMap()
    {
        return [
            VariableExistsQuery::class => [$this, 'queryForVariableExistence'],
            VariableTypeQuery::class => [$this, 'queryForVariableType']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectActNode()
    {
        return $this->subjectNode;
    }

    /**
     * Determines whether the specified variable exists
     *
     * @param VariableExistsQuery $query
     * @return bool|null
     */
    public function queryForVariableExistence(VariableExistsQuery $query)
    {
        if (array_key_exists($query->getVariableName(), $this->variableTypeDeterminers)) {
            // We've discovered that this scope _does_ define the requested variable
            return true;
        }

        // This scope doesn't define the requested variable - return null
        // so that we can bubble up to an ancestor context that does define it
        return null;
    }

    /**
     * Fetches the type of the variable
     *
     * @param VariableTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface|null
     */
    public function queryForVariableType(
        VariableTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        if (array_key_exists($query->getVariableName(), $this->variableTypeDeterminers)) {
            // We've discovered that this scope _does_ define the requested variable,
            // so determine and return its type
            return $this->variableTypeDeterminers[$query->getVariableName()]->determine($validationContext);
        }

        // This scope doesn't define the requested variable - return null
        // so that we can bubble up to an ancestor context that does define it
        return null;
    }
}
