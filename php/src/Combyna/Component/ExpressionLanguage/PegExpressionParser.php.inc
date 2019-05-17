<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\ExpressionLanguage;

use hafriedlander\Peg\Parser\Basic;

/**
 * Class PegExpressionParser
 *
 * A generated PEG parser for parsing the Combyna expression language
 *
 * NB: This class will be modified by php-peg and output to PegExpressionParser.php in the dist directory,
 *     by Combyna\Component\ExpressionLanguage\CacheWarmer\ExpressionLanguageParserCacheWarmer.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PegExpressionParser extends Basic
{
    /*!* PegExpressionParser
    BooleanLiteral: value:/\b(true|false)\b/ >
    NumberLiteral: value:/\d+(\.\d+)?/ >
    StringLiteral: "\'" value:/[^']* / "\'" >
    Variable: variableName:/[\w_]+/ > !/[(:]/ >
    ElementList: Expression > ("," > Expression >)*
        function Expression(&$result, $sub)
        {
            // Called for each matched element
            $result['elements'][] = $sub['node'];
        }
    List: "[" > elements:(:ElementList?) > "]" >
        function elements(&$result, $sub)
        {
            $result['elements'] = isset($sub['ElementList']) ? $sub['ElementList']['elements'] : [];
        }
    AttributeDeclaration: attributeName:/[\w_]+/ > ":" > Expression >
        function attributeName(&$result, $sub)
        {
            $result['attributeName'] = $sub['text'];
        }
        function Expression(&$result, $sub)
        {
            $result['value'] = $sub['node'];
        }
    AttributeList: AttributeDeclaration > ("," > AttributeDeclaration >)*
        function AttributeDeclaration(&$result, $sub)
        {
            // Called for each matched attribute
            $result['attributes'][$sub['attributeName']] = $sub['value'];
        }
    Structure: "{" > attributes:(:AttributeList?) > "}" >
        function attributes(&$result, $sub)
        {
            $result['attributes'] = isset($sub['AttributeList']) ? $sub['AttributeList']['attributes'] : [];
        }
    Value:
            (
                NumberLiteral |
                BooleanLiteral |
                List |
                Structure |
                StringLiteral |
                Builtin |
                Function |
                Variable
            ) > |
            '(' > Expression > ')' >
        function NumberLiteral(&$result, $sub) {
            $result['node'] = [
                'type' => 'number',
                'number' => is_numeric($sub['value']['text']) ? $sub['value']['text'] * 1 : 0
            ];
        }
        function BooleanLiteral(&$result, $sub) {
            $result['node'] = [
                'type' => 'boolean',
                'boolean' => ($sub['value']['text'] === 'true')
            ];
        }
        function List(&$result, $sub) {
            $result['node'] = [
                'type' => 'list',
                'elements' => $sub['elements']
            ];
        }
        function Structure(&$result, $sub) {
            $result['node'] = [
                'type' => 'structure',
                'attributes' => $sub['attributes']
            ];
        }
        function StringLiteral(&$result, $sub) {
            $result['node'] = [
                'type' => 'text',
                'text' => $sub['value']['text']
            ];
        }
        function Builtin(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function Function(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function Variable(&$result, $sub) {
            $result['node'] = [
                'type' => 'variable',
                'variable' => $sub['variableName']['text']
            ];
        }
        function Expression(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
    Attribute: '.' > (attributeName:/[\w_]+/) >
    Dereference: Value > ( Attribute ) *
        function Value(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function Attribute(&$result, $sub) {
            $result['node'] = $this->buildAttributeExpression(
                $result['node'],
                $sub['attributeName']['text']
            );
        }
    Multiplication: '*' > operand:Dereference >
    Division: '/' > operand:Dereference >
    Product: Dereference > ( Multiplication | Division ) *
        function Dereference(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function Multiplication(&$result, $sub) {
            $result['node'] = $this->buildBinaryArithmeticExpression(
                $result['node'],
                '*',
                $sub['operand']['node']
            );
        }
        function Division(&$result, $sub) {
            $result['node'] = $this->buildBinaryArithmeticExpression(
                $result['node'],
                '/',
                $sub['operand']['node']
            );
        }
    Addition: '+' > operand:Product >
    Subtraction: '-' > operand:Product >
    Sum: Product > ( Addition | Subtraction ) *
        function Product(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function Addition(&$result, $sub) {
            $result['node'] = $this->buildBinaryArithmeticExpression(
                $result['node'],
                '+',
                $sub['operand']['node']
            );
        }
        function Subtraction(&$result, $sub) {
            $result['node'] = $this->buildBinaryArithmeticExpression(
                $result['node'],
                '-',
                $sub['operand']['node']
            );
        }
    Append: '~' > operand:Sum >
    Concatenation: Sum > ( Append ) *
        function Sum(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function Append(&$result, $sub) {
            if (isset($result['node']['type']) && $result['node']['type'] === 'concatenation') {
                $result['node']['list']['elements'][] = $sub['operand']['node'];
            } else {
                $result['node'] = [
                    'type' => 'concatenation',
                    'list' => [
                        'type' => 'list',
                        'elements' => [
                            $result['node'],
                            $sub['operand']['node']
                        ]
                    ]
                ];
            }
        }
    Equal: '=' > operand:Concatenation >
    Unequal: '<>' > operand:Concatenation >
    CaseInsensitivelyEqual: '~=' > operand:Concatenation >
    CaseInsensitivelyUnequal: '~<>' > operand:Concatenation >
    Comparison: Concatenation > ( Equal | Unequal | CaseInsensitivelyEqual | CaseInsensitivelyUnequal ) *
        function Concatenation(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function Equal(&$result, $sub) {
            $result['node'] = $this->buildComparisonExpression(
                $result['node'],
                '=',
                $sub['operand']['node']
            );
        }
        function Unequal(&$result, $sub) {
            $result['node'] = $this->buildComparisonExpression(
                $result['node'],
                '<>',
                $sub['operand']['node']
            );
        }
        function CaseInsensitivelyEqual(&$result, $sub) {
            $result['node'] = $this->buildComparisonExpression(
                $result['node'],
                '~=',
                $sub['operand']['node']
            );
        }
        function CaseInsensitivelyUnequal(&$result, $sub) {
            $result['node'] = $this->buildComparisonExpression(
                $result['node'],
                '~<>',
                $sub['operand']['node']
            );
        }
    NamedArgument: parameterName:/[\w_]+/ > ":" > Expression >
        function parameterName(&$result, $sub)
        {
            $result['parameterName'] = $sub['text'];
        }
        function Expression(&$result, $sub)
        {
            $result['argument'] = $sub['node'];
        }
    PositionalArgument: Expression >
        function Expression(&$result, $sub)
        {
            $result['argument'] = $sub['node'];
        }
    PositionalArgumentList: PositionalArgument > ("," > PositionalArgument >)*
        function PositionalArgument(&$result, $sub)
        {
            // Called for each matched argument
            $result['arguments'][] = $sub['argument'];
        }
    NamedArgumentList: NamedArgument > ("," > NamedArgument >)*
        function NamedArgument(&$result, $sub)
        {
            // Called for each matched argument
            $result['arguments'][$sub['parameterName']] = $sub['argument'];
        }
    Builtin: (builtinName:/[\w_]+/) >
            "(" >
            (
                (PositionalArgumentList > "," > NamedArgumentList) |
                NamedArgumentList |
                PositionalArgumentList
            )? >
            ")" >
        function builtinName(&$result, $sub)
        {
            $result['node'] = [
                'type' => 'builtin',
                'name' => $sub['text'],
                'positional-arguments' => [],
                'named-arguments' => []
            ];
        }
        function PositionalArgumentList(&$result, $sub)
        {
            $result['node']['positional-arguments'] = $sub['arguments'];
        }
        function NamedArgumentList(&$result, $sub)
        {
            $result['node']['named-arguments'] = $sub['arguments'];
        }
    Function: locator:((libraryName:/[\w_]+/) "." (functionName:/[\w_]+/)) > "(" > NamedArgumentList? ")" >
        function locator(&$result, $sub)
        {
            $result['node'] = [
                'type' => 'function',
                'library' => $sub['libraryName']['text'],
                'name' => $sub['functionName']['text'],
                'arguments' => []
            ];
        }
        function NamedArgumentList(&$result, $sub)
        {
            $result['node']['arguments'] = $sub['arguments'];
        }
    ConsequentAndAlternate: '?' > consequent:Conditional > ':' > alternate:Conditional >
    Conditional: Comparison > ( ConsequentAndAlternate ) *
        function Comparison(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
        function ConsequentAndAlternate(&$result, $sub) {
            $result['node'] = $this->buildTernaryConditionalExpression(
                $result['node'],
                $sub['consequent']['node'],
                $sub['alternate']['node']
            );
        }
    Expression: > Conditional
        function Conditional(&$result, $sub) {
            $result['node'] = $sub['node'];
        }
    */

    // "Conditional" just happens to currently be the expression with the lowest precedence,
    // which is why the top-level Expression references it

    /**
     * @param array $structure
     * @param string $attributeName
     * @return array
     */
    private function buildAttributeExpression(array $structure, $attributeName)
    {
        return [
            'type' => 'attribute',
            'structure' => $structure,
            'attribute' => $attributeName
        ];
    }

    /**
     * @param array $left
     * @param string $operator
     * @param array $right
     * @return array
     */
    private function buildBinaryArithmeticExpression(array $left, $operator, array $right)
    {
        return [
            'type' => 'binary-arithmetic',
            'operator' => $operator,
            'left' => $left,
            'right' => $right
        ];
    }

    /**
     * @param array $left
     * @param string $operator
     * @param array $right
     * @return array
     */
    private function buildComparisonExpression(array $left, $operator, array $right)
    {
        return [
            'type' => 'comparison',
            'operator' => $operator,
            'left' => $left,
            'right' => $right
        ];
    }

    /**
     * @param array $condition
     * @param array $consequent
     * @param array $alternate
     * @return array
     */
    private function buildTernaryConditionalExpression(array $condition, array $consequent, array $alternate)
    {
        return [
            'type' => 'conditional',
            'condition' => $condition,
            'consequent' => $consequent,
            'alternate' => $alternate
        ];
    }

    /**
     * Copied from \hafriedlander\Peg\Parser\Basic.
     * Edited to allow newlines to be matched by whitespace gobbling.
     *
     * @return bool|string
     */
    public function whitespace()
    {
        $matched = preg_match('/\s+/', $this->string, $matches, PREG_OFFSET_CAPTURE, $this->pos);

        if ($matched && $matches[0][1] === $this->pos) {
            $this->pos += strlen($matches[0][0]);

            return $matches[0][0];
        }

        return false;
    }
}
