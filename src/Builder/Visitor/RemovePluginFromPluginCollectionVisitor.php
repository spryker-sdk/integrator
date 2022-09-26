<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class RemovePluginFromPluginCollectionVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected const STATEMENT_CLASS_METHOD = 'Stmt_ClassMethod';

    /**
     * @var \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected $classMetadataTransfer;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     */
    public function __construct(ClassMetadataTransfer $classMetadataTransfer)
    {
        $this->classMetadataTransfer = $classMetadataTransfer;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    public function enterNode(Node $node): Node
    {
        if (
            $node->getType() === static::STATEMENT_CLASS_METHOD
            && $node->name->toString() === $this->classMetadataTransfer->getTargetMethodNameOrFail()
        ) {
            $pluginToRemoveIndex = null;
            foreach ($node->stmts as $index => $stmt) {
                if (
                    $stmt->expr instanceof MethodCall === false
                    || strpos(strtolower($stmt->expr->name->toString()), 'add') === false
                ) {
                    continue;
                }

                /** @var \PhpParser\Node\Arg $arg */
                foreach ($stmt->expr->args as $arg) {
                    if ($arg->value instanceof New_ && $arg->value->class->toString() === $this->classMetadataTransfer->getSourceOrFail()) {
                        $pluginToRemoveIndex = $index;

                        break 2;
                    }
                }
            }

            if ($pluginToRemoveIndex !== null) {
                array_splice($node->stmts, $pluginToRemoveIndex, 1);
            }

            return $node;
        }

        return $node;
    }
}
