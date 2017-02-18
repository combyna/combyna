<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\ExpressionLanguage;

use hafriedlander\Peg\Parser\Basic;

/**
 * Class PegExpressionParser
 *
 * A generated PEG parser for parsing the Combyna expression language
 *
 * NB: This class will be modified by php-peg and output to PegExpressionParser.php in this directory,
 *     by `composer run build:expression-parser`.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PegExpressionParser extends Basic
{
    /*!* PegExpressionParser
    BooleanLiteral: /\b(true|false)\b/ >
    NumberLiteral: /[0-9]+/ >
    StringLiteral: "\'" inside:/[^']* / "\'" >
    Value: (NumberLiteral | BooleanLiteral | StringLiteral) > | '(' > Expr > ')' >
        function NumberLiteral( &$result, $sub ) {
            $result['node'] = [
                'type' => 'number',
                'number' => (float) $sub['text']
            ];
        }
        function BooleanLiteral( &$result, $sub ) {
            $result['node'] = [
                'type' => 'boolean',
                'boolean' => ($sub['text'] === 'true')
            ];
        }
        function StringLiteral( &$result, $sub ) {
            $result['node'] = [
                'type' => 'string',
                'string' => $sub['inside']['text']
            ];
        }
        function Expr( &$result, $sub ) {
            $result['node'] = $sub['node'] ;
        }
    Multiplication: '*' > operand:Value >
    Division: '/' > operand:Value >
    Product: Value > ( Multiplication | Division ) *
        function Value( &$result, $sub ) {
            $result['node'] = $sub['node'] ;
        }
        function Multiplication( &$result, $sub ) {
            $result['node'] = $this->buildBinaryArithmeticExpression(
                $result['node'],
                '*',
                $sub['operand']['node']
            );
        }
        function Division( &$result, $sub ) {
            $result['node'] = $this->buildBinaryArithmeticExpression(
                $result['node'],
                '/',
                $sub['operand']['node']
            );
        }
    Addition: '+' > operand:Product >
    Subtraction: '-' > operand:Product >
    Sum: Product > ( Addition | Subtraction ) *
        function Product( &$result, $sub ) {
            $result['node'] = $sub['node'] ;
        }
        function Addition( &$result, $sub ) {
            $result['node'] = $this->buildBinaryArithmeticExpression(
                $result['node'],
                '+',
                $sub['operand']['node']
            );
        }
        function Subtraction( &$result, $sub ) {
            $result['node'] = $this->buildBinaryArithmeticExpression(
                $result['node'],
                '-',
                $sub['operand']['node']
            );
        }
    Equal: '=' > operand:Sum >
    Unequal: '<>' > operand:Sum >
    Comparison: Sum > ( Equal | Unequal ) *
        function Sum( &$result, $sub ) {
            $result['node'] = $sub['node'] ;
        }
        function Equal( &$result, $sub ) {
            $result['node'] = $this->buildComparisonExpression(
                $result['node'],
                '=',
                $sub['operand']['node']
            );
        }
        function Unequal( &$result, $sub ) {
            $result['node'] = $this->buildComparisonExpression(
                $result['node'],
                '<>',
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
    SingleArgument: NamedArgument >
        function NamedArgument(&$result, $sub)
        {
            $result['arguments'] = [
                $sub['parameterName'] => $sub['argument']
            ];
        }
    ArgumentList: NamedArgument > ("," > NamedArgument >)+
        function NamedArgument(&$result, $sub)
        {
            // Called for each matched argument
            $result['arguments'][$sub['parameterName']] = $sub['argument'];
        }
    Function: (functionName:/[\w_]+/) > "(" > (ArgumentList | SingleArgument)? ")" >
        function functionName(&$result, $sub)
        {
            $result['node'] = [
                'type' => 'function',
                'name' => $sub['text'],
                'arguments' => []
            ];
        }
        function ArgumentList(&$result, $sub)
        {
            $result['node']['arguments'] = $sub['arguments'];
        }
        function SingleArgument(&$result, $sub)
        {
            $result['node']['arguments'] = $sub['arguments'];
        }
    Expression: > (Comparison | StringLiteral | Function)
        function Comparison( &$result, $sub ) {
            $result['node'] = $sub['node'];
        }
        function StringLiteral( &$result, $sub ) {
            $result['node'] = $sub['node'];
        }
        function Function( &$result, $sub ) {
            $result['node'] = $sub['node'];
        }
    */

    /**
     * @param string $left
     * @param string $operator
     * @param string $right
     * @return array
     */
    private function buildBinaryArithmeticExpression($left, $operator, $right)
    {
        return [
            'type' => 'binary-arithmetic',
            'operator' => $operator,
            'left' => $left,
            'right' => $right
        ];
    }

    /**
     * @param string $left
     * @param string $operator
     * @param string $right
     * @return array
     */
    private function buildComparisonExpression($left, $operator, $right)
    {
        return [
            'type' => 'comparison',
            'operator' => $operator,
            'left' => $left,
            'right' => $right
        ];
    }





    /*DIS!* Calculator
    Number: /[0-9]+/
    Value: Number > | '(' > Expr > ')' >
        function Number( &$result, $sub ) {
            $result['val'] = $sub['text'] ;
        }
        function Expr( &$result, $sub ) {
            $result['val'] = $sub['val'] ;
        }
    Times: '*' > operand:Value >
    Div: '/' > operand:Value >
    Product: Value > ( Times | Div ) *
        function Value( &$result, $sub ) {
            $result['val'] = $sub['val'] ;
        }
        function Times( &$result, $sub ) {
            $result['val'] *= $sub['operand']['val'] ;
        }
        function Div( &$result, $sub ) {
            $result['val'] /= $sub['operand']['val'] ;
        }
    Plus: '+' > operand:Product >
    Minus: '-' > operand:Product >
    Sum: Product > ( Plus | Minus ) *
        function Product( &$result, $sub ) {
            $result['val'] = $sub['val'] ;
        }
        function Plus( &$result, $sub ) {
            $result['val'] += $sub['operand']['val'] ;
        }
        function Minus( &$result, $sub ) {
            $result['val'] -= $sub['operand']['val'] ;
        }
    Expr: Sum
        function Sum( &$result, $sub ) {
            $result['val'] = $sub['val'] ;
        }
    */
}
