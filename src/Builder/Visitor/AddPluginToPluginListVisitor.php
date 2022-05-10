<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\New_;
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Helper\ClassHelper;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class AddPluginToPluginListVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected const STATEMENT_ARRAY = 'Expr_Array';

    /**
     * @var string
     */
    protected const STATEMENT_CLASS_METHOD = 'Stmt_ClassMethod';

    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected $classMetadataTransfer;

    /**
     * @var bool
     */
    protected $methodFound = false;

    /**
     * @param string $methodName
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     */
    public function __construct(string $methodName, ClassMetadataTransfer $classMetadataTransfer)
    {
        $this->methodName = $methodName;
        $this->classMetadataTransfer = $classMetadataTransfer;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    public function enterNode(Node $node): Node
    {
        if ($node->getType() === static::STATEMENT_CLASS_METHOD && $node->name->toString() === $this->methodName) {
            $this->methodFound = true;

            return $node;
        }

        if ($this->methodFound && $node->getType() === static::STATEMENT_ARRAY) {
            $this->addNewPlugin($node);
            $this->methodFound = false;
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    protected function addNewPlugin(Node $node): Node
    {
        if ($this->isPluginAdded($node)) {
            return $node;
        }

        $items = [];
        $itemAdded = false;
        foreach ($node->items as $item) {
            $nodeClassName = $item->value->class->toString();
            if ($nodeClassName === $this->classMetadataTransfer->getBeforeOrFail()) {
                $items[] = $this->createArrayItemWithInstanceOf();
                $items[] = $item;
                $itemAdded = true;

                continue;
            }
            if ($nodeClassName === $this->classMetadataTransfer->getAfterOrFail()) {
                $items[] = $item;
                $items[] = $this->createArrayItemWithInstanceOf();
                $itemAdded = true;

                continue;
            }

            $items[] = $item;
        }

        if (!$itemAdded) {
            $items[] = $this->createArrayItemWithInstanceOf();
        }

        $node->items = $items;

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return bool
     */
    protected function isPluginAdded(Node $node): bool
    {
        foreach ($node->items as $item) {
            if (!($item->value instanceof New_)) {
                continue;
            }
            $nodeClassName = $item->value->class->toString();
            if ($nodeClassName === $this->classMetadataTransfer->getSourceOrFail()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \PhpParser\Node\Expr\ArrayItem
     */
    protected function createArrayItemWithInstanceOf(): ArrayItem
    {
        return new ArrayItem(
            (new BuilderFactory())->new(
                (new ClassHelper())->getShortClassName($this->classMetadataTransfer->getSourceOrFail()),
            ),
        );
    }
}
