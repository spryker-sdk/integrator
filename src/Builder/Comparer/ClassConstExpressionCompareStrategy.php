<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Comparer;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\UseItem;
use SprykerSdk\Integrator\Builder\Finder\UseStatementsFinder\UseStatementsFinderInterface;

class ClassConstExpressionCompareStrategy implements CompareStrategyInterface
{
    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\UseStatementsFinder\UseStatementsFinderInterface
     */
    protected UseStatementsFinderInterface $useStatementsFinder;

    /**
     * @param \SprykerSdk\Integrator\Builder\Finder\UseStatementsFinder\UseStatementsFinderInterface $useStatementsFinder
     */
    public function __construct(UseStatementsFinderInterface $useStatementsFinder)
    {
        $this->useStatementsFinder = $useStatementsFinder;
    }

    /**
     * @param \PhpParser\Node\Expr\ClassConstFetch $node
     * @param \PhpParser\Node\Expr\ClassConstFetch $nodeToCompare
     * @param array<\PhpParser\Node> $classTokenTree
     *
     * @return bool
     */
    public function isEqual(Node $node, Node $nodeToCompare, array $classTokenTree = []): bool
    {
        if (!($node instanceof ClassConstFetch) || !($nodeToCompare instanceof ClassConstFetch)) {
            return false;
        }

        if (!($node->name instanceof Identifier) || !($nodeToCompare->name instanceof Identifier)) {
            return false;
        }

        if ($node->name->toString() !== $nodeToCompare->name->toString()) {
            return false;
        }

        if (!($node->class instanceof Name) || !($nodeToCompare->class instanceof Name)) {
            return false;
        }

        return $this->isClassNameEqual($node->class, $nodeToCompare->class, $classTokenTree);
    }

    /**
     * @param \PhpParser\Node\Name $className
     * @param \PhpParser\Node\Name $classNameToCompare
     * @param array<\PhpParser\Node> $classTokenTree
     *
     * @return bool
     */
    protected function isClassNameEqual(Name $className, Name $classNameToCompare, array $classTokenTree): bool
    {
        $useClassNames = $this->getUseClassNames($classTokenTree);

        return $this->getFQCN($className, $useClassNames) === $this->getFQCN($classNameToCompare, $useClassNames);
    }

    /**
     * @param \PhpParser\Node\Name $className
     * @param array<string> $useClassNames
     *
     * @return string
     */
    protected function getFQCN(Name $className, array $useClassNames): string
    {
        if ($className instanceof FullyQualified) {
            return $className->toString();
        }

        $shortClassName = $className->toString();

        foreach ($useClassNames as $useClassName) {
            if (substr($useClassName, 0 - strlen($shortClassName)) === $shortClassName) {
                return $useClassName;
            }
        }

        return $shortClassName;
    }

    /**
     * @param array<\PhpParser\Node> $classTokenTree
     *
     * @return array<string>
     */
    protected function getUseClassNames(array $classTokenTree): array
    {
        $names = [];

        $useStatements = $this->useStatementsFinder->findUseStatements($classTokenTree);

        foreach ($useStatements as $useStatement) {
            $names[] = array_map(static fn (UseItem $use): string => $use->name->toString(), $useStatement->uses);
        }

        return array_merge(...$names);
    }

    /**
     * @param \PhpParser\Node $node
     * @param \PhpParser\Node $nodeToCompare
     *
     * @return bool
     */
    public function isApplicable(Node $node, Node $nodeToCompare): bool
    {
        return $node instanceof ClassConstFetch && $nodeToCompare instanceof ClassConstFetch;
    }
}
