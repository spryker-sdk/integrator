<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\ClassModifier\GlueRelationship;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface;
use SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface;
use SprykerSdk\Integrator\Helper\ClassHelperInterface;

abstract class AbstractModifier
{
    /**
     * @var \PhpParser\NodeTraverser
     */
    protected $nodeTraverser;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface
     */
    protected $commonClassModifier;

    /**
     * @var \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface
     */
    protected $classNodeFinder;

    /**
     * @var \SprykerSdk\Integrator\Helper\ClassHelperInterface
     */
    protected $classHelper;

    /**
     * @var \PhpParser\BuilderFactory
     */
    protected $builderFactory;

    /**
     * @param \PhpParser\NodeTraverser $nodeTraverser
     * @param \SprykerSdk\Integrator\Builder\ClassModifier\CommonClass\CommonClassModifierInterface $commonClassModifier
     * @param \SprykerSdk\Integrator\Builder\Finder\ClassNodeFinderInterface $classNodeFinder
     * @param \SprykerSdk\Integrator\Helper\ClassHelperInterface $classHelper
     * @param \PhpParser\BuilderFactory $builderFactory
     */
    public function __construct(
        NodeTraverser $nodeTraverser,
        CommonClassModifierInterface $commonClassModifier,
        ClassNodeFinderInterface $classNodeFinder,
        ClassHelperInterface $classHelper,
        BuilderFactory $builderFactory
    ) {
        $this->nodeTraverser = $nodeTraverser;
        $this->commonClassModifier = $commonClassModifier;
        $this->classNodeFinder = $classNodeFinder;
        $this->builderFactory = $builderFactory;
        $this->classHelper = $classHelper;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $classMethod
     * @param string $key
     * @param string $classNameToAdd
     *
     * @return bool
     */
    protected function isRelationshipExists(ClassMethod $classMethod, string $key, string $classNameToAdd): bool
    {
        $key = ltrim($key, '\\');
        $classNameToAdd = ltrim($classNameToAdd, '\\');

        /** @var array<\PhpParser\Node> $nodes */
        $nodes = $classMethod->stmts;

        /** @var \PhpParser\Node\Stmt\ClassMethod|null $node */
        $node = (new NodeFinder())->findFirst(
            $nodes,
            function (Node $node) use ($key, $classNameToAdd) {
                if (!($node instanceof MethodCall) || count($node->args) !== 2) {
                    return false;
                }
                if (
                    !($node->args[0]->value instanceof ClassConstFetch)
                    || $node->args[0]->value->class->toString() . '::' . $node->args[0]->value->name !== $key
                ) {
                    return false;
                }

                return ($node->args[1]->value instanceof New_) && $node->args[1]->value->class->toString() === $classNameToAdd;
            },
        );

        return (bool)$node;
    }
}
