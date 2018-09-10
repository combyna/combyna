<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Plugin\Core\Type\Function_;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Expression\Config\Act\FunctionExpressionNode;
use Combyna\Component\Type\AnyType;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Class ListConcatReturnTypeDeterminer
 *
 * Determines the return type of a call to `list.concat(...)` based on the types
 * of the arguments passed to it. Because the resulting array will be a combination
 * of the elements from all input arrays, the type of its elements can differ
 * depending on their element types.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ListConcatReturnTypeDeterminer implements TypeDeterminerInterface
{
    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        $node = $validationContext->getSubjectActNode();

        if (!$node instanceof FunctionExpressionNode) {
            return new StaticListType(new AnyType());
        }

        $listsArgExpression = $node->getArgumentExpressionBag()->getExpression('lists');
        $listsArgResultType = $listsArgExpression->getResultTypeDeterminer()->determine($validationContext);

        if (!$listsArgResultType instanceof StaticListType) {
            return new UnresolvedType(sprintf(
                'list.concat type - expected `lists` arg to resolve to a %s but got %s',
                StaticListType::class,
                get_class($listsArgResultType)
            ));
        }

        $listTypes = $listsArgResultType->getElementType();

        return $listTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
