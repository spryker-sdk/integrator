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
     * @param \PhpParser\Node\Expr\Array_ $node
     *
     * @return string
     */
    protected function pExpr_Array(Array_ $node)
    {
        return '[' . $this->pCommaSeparatedMultiline($node->items, true) . $this->nl . ']';
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
}
