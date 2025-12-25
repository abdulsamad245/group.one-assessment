<?php

/**
 * Custom PHPStan Rule - NoUpdateOptionRule
 *
 * Disallows update_option function, enforcing Option object set() method instead.
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
 * Custom PHPStan rule: Disallow update_option, enforce Option object set()
 *
 * @implements Rule<FuncCall>
 */
class NoUpdateOptionRule implements Rule
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

        if ($functionName === 'update_option') {
            return [
                RuleErrorBuilder::message(
                    'Function update_option() is not allowed. Use Option object set() method instead.'
                )->build(),
            ];
        }

        return [];
    }
}
