<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Printer;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Nop;
use PhpParser\PrettyPrinter\Standard;

class ClassPrinter extends Standard
{
    /**
     * Determine whether a list of nodes uses multiline formatting.
     *
     * @param (\PhpParser\Node|null)[] $nodes Node list
     *
     * @return bool Whether multiline formatting is used
     */
    protected function isMultiline(array $nodes): bool
    {
        if (!$nodes) {
            return false;
        }

        if (count($nodes) === 1 && current($nodes) !== null) {
            $node = current($nodes);
            $startPos = $node->getStartTokenPos() - 1;
            $endPos = $node->getEndTokenPos() + 1;
            $text = $this->origTokens->getTokenCode($startPos, $endPos, 0);
            if (false === strpos($text, "\n")) {
                return false;
            }

            return true;
        }

        return parent::isMultiline($nodes);
    }

    /**
     * @param \PhpParser\Node\Expr\Array_ $node
     *
     * @return string
     */
    protected function pExpr_Array(Array_ $node): string
    {
        /** @var array<\PhpParser\Node> $items */
        $items = $node->items;

        return '[' . $this->pCommaSeparatedMultiline($items, true) . $this->nl . ']';
    }

    /**
     * Pretty prints an array of nodes (statements) and indents them optionally.
     *
     * @param array<\PhpParser\Node> $nodes Array of nodes
     * @param bool $indent Whether to indent the printed nodes
     *
     * @return string Pretty printed statements
     */
    protected function pStmts(array $nodes, bool $indent = true): string
    {
        if ($indent) {
            $this->indent();
        }

        $result = '';
        foreach ($nodes as $key => $node) {
            if ($key !== 0 && $node instanceof ClassMethod) {
                $result .= PHP_EOL;
            }

            if ($node instanceof Class_) {
                $result .= $this->nl;
            }

            $comments = $node->getComments();
            if ($comments) {
                $result .= $this->nl . $this->pComments($comments);
                if ($node instanceof Nop) {
                    continue;
                }
            }

            $result .= $this->nl . $this->p($node);
        }

        if ($indent) {
            $this->outdent();
        }

        return $result;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function pSingleQuotedString(string $string): string
    {
        return '\'' . preg_replace("/'|\\\\(?=[\\\\']|$)/", '\\\\$0', $string) . '\'';
    }
}
