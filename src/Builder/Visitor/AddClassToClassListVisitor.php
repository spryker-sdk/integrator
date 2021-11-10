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
use PhpParser\NodeVisitorAbstract;
use SprykerSdk\Integrator\Helper\ClassHelper;

class AddClassToClassListVisitor extends NodeVisitorAbstract
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
     * @var string
     */
    protected $className;

    /**
     * @var bool
     */
    protected $methodFound = false;

    /**
     * @var string
     */
    protected $constantName;

    /**
     * @param string $methodName
     * @param string $className
     * @param string $constantName
     */
    public function __construct(string $methodName, string $className, string $constantName)
    {
        $this->methodName = $methodName;
        $this->className = ltrim($className, '\\');
        $this->constantName = $constantName;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node|int|null
     */
    public function enterNode(Node $node)
    {
        if ($node->getType() === static::STATEMENT_CLASS_METHOD && $node->name->toString() === $this->methodName) {
            $this->methodFound = true;

            return $node;
        }

        if ($this->methodFound && $node->getType() === static::STATEMENT_ARRAY) {
            $this->addClass($node);
            $this->methodFound = false;
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    protected function addClass(Node $node): Node
    {
        if ($this->isClassAdded($node)) {
            return $node;
        }

        $node->items[] = $this->createArrayItemWithInstanceOf();

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return bool
     */
    protected function isClassAdded(Node $node): bool
    {
        foreach ($node->items as $item) {
            $nodeClassName = $item->value->class->toString();
            if ($nodeClassName === $this->className) {
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
            (new BuilderFactory())->classConstFetch(
                (new ClassHelper())->getShortClassName($this->className),
                $this->constantName,
            ),
        );
    }
}
