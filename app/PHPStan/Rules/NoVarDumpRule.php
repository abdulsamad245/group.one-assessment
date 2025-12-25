<?php

/**
 * Custom PHPStan Rule - NoVarDumpRule
 *
 * Disallows var_dump, print_r, dd, and dump functions in production code.
 *
 * @package App\PHPStan\Rules
 */

namespace App\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Custom PHPStan rule: Disallow var_dump and print_r
 *
 * @implements Rule<FuncCall>
 */
class NoVarDumpRule implements Rule
{
    /**
     * Get the node type this rule applies to.
     *
     * @return string
     */
    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * Process the node and return any errors.
     *
     * @param FuncCall $node
     * @param Scope $scope
     * @return array<\PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Name) {
            return [];
        }

        $functionName = $node->name->toString();

        if (in_array($functionName, ['var_dump', 'print_r', 'dd', 'dump'], true)) {
            return [
                RuleErrorBuilder::message(
                    sprintf('Function %s() is not allowed in production code.', $functionName)
                )->build(),
            ];
        }

        return [];
    }
}
