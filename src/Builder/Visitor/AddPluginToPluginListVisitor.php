<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter\Standard;
use SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface;
use SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface;
use SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface;
use SprykerSdk\Integrator\Transfer\ClassMetadataTransfer;

class AddPluginToPluginListVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    public const STMTS = 'stmts';

    /**
     * @var string
     */
    protected const ARRAY_MERGE_FUNCTION = 'array_merge';

    /**
     * @var string
     */
    protected const PLUGINS_VARIBLE = 'plugins';

    /**
     * @var string
     */
    protected const STATEMENT_CLASS_METHOD = 'Stmt_ClassMethod';

    /**
     * @var \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer
     */
    protected $classMetadataTransfer;

    /**
     * @var \SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface
     */
    protected PluginPositionResolverInterface $pluginPositionResolver;

    /**
     * @var bool
     */
    protected $methodFound = false;

    /**
     * @var \PhpParser\Node\Stmt\Expression|null
     */
    protected ?Expression $indexExpr;

    /**
     * @var \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface
     */
    protected ClassLoaderInterface $classLoader;

    /**
     * @var \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface
     */
    protected ArgumentBuilderInterface $argumentBuilder;

    /**
     * @param \SprykerSdk\Integrator\Transfer\ClassMetadataTransfer $classMetadataTransfer
     * @param \SprykerSdk\Integrator\Builder\Visitor\PluginPositionResolver\PluginPositionResolverInterface $pluginPositionResolver
     * @param \SprykerSdk\Integrator\Builder\ArgumentBuilder\ArgumentBuilderInterface $argumentBuilder
     * @param \SprykerSdk\Integrator\Builder\ClassLoader\ClassLoaderInterface $classLoader
     * @param \PhpParser\Node\Stmt\Expression|null $indexExpr
     */
    public function __construct(
        ClassMetadataTransfer $classMetadataTransfer,
        PluginPositionResolverInterface $pluginPositionResolver,
        ArgumentBuilderInterface $argumentBuilder,
        ClassLoaderInterface $classLoader,
        ?Expression $indexExpr = null
    ) {
        $this->classMetadataTransfer = $classMetadataTransfer;
        $this->pluginPositionResolver = $pluginPositionResolver;
        $this->argumentBuilder = $argumentBuilder;
        $this->classLoader = $classLoader;
        $this->indexExpr = $indexExpr;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node|int
     */
    public function leaveNode(Node $node)
    {
        if ($this->methodFound && $node instanceof ClassMethod && $node->name->toString() === $this->classMetadataTransfer->getTargetMethodNameOrFail()) {
            $this->addNewPluginInToAssignList($node);

            $this->methodFound = false;
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node|int
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof ClassMethod && $node->name->toString() === $this->classMetadataTransfer->getTargetMethodNameOrFail()) {
            $this->methodFound = true;
            if ($this->classMetadataTransfer->getCondition()) {
                return $this->addNewPluginWithConditionIntoList($node);
            }

            return $node;
        }

        if ($this->methodFound) {
            if ($node instanceof FuncCall && $this->isArrayMergeFuncCallNode($node)) {
                $this->addNewPluginIntoArrayMergeFuncNode($node);

                return $this->successfullyProcessed();
            }

            if ($node instanceof If_ && $this->classMetadataTransfer->getCondition() !== null) {
                $this->addNewPluginIntoIfCondition($node);

                return $this->successfullyProcessed();
            }

            if ($node instanceof Array_ && $this->classMetadataTransfer->getCondition() === null) {
                $this->addNewPlugin($node);

                return $this->successfullyProcessed();
            }
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return \PhpParser\Node
     */
    protected function addNewPluginInToAssignList(ClassMethod $node): Node
    {
        $newStmts = (array)$node->stmts;
        $returnStmt = array_pop($newStmts);
        if (
            !($returnStmt instanceof Return_) ||
            !($returnStmt->expr instanceof Variable)
        ) {
            return $node;
        }

        $newStmts[] = $this->getAssignPlugin($returnStmt->expr);
        $newStmts[] = $returnStmt;
        $node->stmts = $newStmts;

        return $node;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return \PhpParser\Node
     */
    protected function addNewPluginWithConditionIntoList(ClassMethod $node): Node
    {
        foreach ((array)$node->stmts as $stmt) {
            if ($stmt instanceof If_ && $this->checkIfConditionExist($stmt)) {
                return $node;
            }
        }
        $newStmts = (array)$node->stmts;
        $returnStmt = array_pop($newStmts);
        if (!($returnStmt instanceof Return_)) {
            return $node;
        }

        if ($returnStmt->expr instanceof Variable) {
            $newStmts[] = $this->createNewConditionStatement($returnStmt->expr);
        }
        if ($returnStmt->expr instanceof FuncCall || $returnStmt->expr instanceof Array_) {
            $newStmts[] = $this->createAssignEmptyArray(static::PLUGINS_VARIBLE);
            $newStmts[] = $this->createNewConditionStatement((new BuilderFactory())->var(static::PLUGINS_VARIBLE));
            $returnStmt->expr = (new BuilderFactory())->var(static::PLUGINS_VARIBLE);
        }
        $newStmts[] = $returnStmt;
        $node->stmts = $newStmts;

        return $node;
    }

    /**
     * @param string $varName
     *
     * @return \PhpParser\Node\Stmt\Expression
     */
    protected function createAssignEmptyArray(string $varName): Expression
    {
        return new Expression(
            new Assign(
                (new BuilderFactory())->var($varName),
                new Array_(),
            ),
        );
    }

    /**
     * @param \PhpParser\Node\Expr $name
     *
     * @return \PhpParser\Node\Stmt
     */
    protected function createNewConditionStatement(Expr $name): Stmt
    {
        return new If_(
            new ConstFetch(
                new Name((string)$this->classMetadataTransfer->getCondition()),
            ),
            [
                static::STMTS => [
                    $this->getAssignPlugin($name),
                ],
            ],
        );
    }

    /**
     * @param \PhpParser\Node\Expr $name
     *
     * @return \PhpParser\Node\Stmt\Expression
     */
    protected function getAssignPlugin(Expr $name): Expression
    {
        return new Expression(
            new Assign(
                new ArrayDimFetch($name),
                $this->createArrayItemWithInstanceOf(),
            ),
        );
    }

    /**
     * @param \PhpParser\Node\Stmt\If_ $ifCondition
     *
     * @return bool
     */
    protected function checkIfConditionExist(If_ $ifCondition): bool
    {
        if ($this->getIfClausePrettyPrint($ifCondition) === $this->classMetadataTransfer->getCondition()) {
            return true;
        }

        foreach ($ifCondition->stmts as $stmt) {
            if ($stmt instanceof If_) {
                return $this->checkIfConditionExist($stmt);
            }
        }

        return false;
    }

    /**
     * @param \PhpParser\Node\Stmt\If_ $ifCondition
     *
     * @return string
     */
    protected function getIfClausePrettyPrint(If_ $ifCondition): string
    {
        $prettyPrinter = new Standard();

        return $prettyPrinter->prettyPrintExpr($ifCondition->cond);
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     *
     * @return bool
     */
    protected function isArrayMergeFuncCallNode(FuncCall $node): bool
    {
        return $node->name instanceof Name && $node->name->parts[0] === static::ARRAY_MERGE_FUNCTION;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     *
     * @return \PhpParser\Node
     */
    protected function addNewPluginIntoArrayMergeFuncNode(FuncCall $node): Node
    {
        if ($this->isPluginAddedInArrayMerge($node)) {
            return $node;
        }

        foreach ($node->args as $arg) {
            if ($arg->value instanceof Array_) {
                $this->addNewPlugin($arg->value);

                return $node;
            }
        }

        $node->args[] = new Arg($this->createArrayWithInstanceOf());

        return $node;
    }

    /**
     * @param \PhpParser\Node\Stmt\If_ $node
     *
     * @return \PhpParser\Node
     */
    protected function addNewPluginIntoIfCondition(If_ $node): Node
    {
        if ($this->getIfClausePrettyPrint($node) === $this->classMetadataTransfer->getCondition()) {
            $this->addNewPluginInstance($node);

            return $node;
        }

        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof If_) {
                return $this->addNewPluginIntoIfCondition($stmt);
            }
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node\Stmt\If_ $statement
     *
     * @return void
     */
    protected function addNewPluginInstance(If_ $statement): void
    {
        foreach ($statement->stmts as $stmt) {
            if ($stmt instanceof Expression && $stmt->expr instanceof Assign) {
                if ($stmt->expr->expr instanceof ArrayItem && $this->isArrayItemEqual($stmt->expr->expr)) {
                    continue;
                }

                array_unshift($statement->stmts, $this->createNewAssignStatement($stmt->expr));

                break;
            }
        }
    }

    /**
     * @param \PhpParser\Node\Expr\Assign $reference
     *
     * @return \PhpParser\Node\Stmt\Expression
     */
    protected function createNewAssignStatement(Assign $reference): Expression
    {
        return new Expression(new Assign($reference->var, (new BuilderFactory())->new(
            $this->classMetadataTransfer->getSourceOrFail(),
        )));
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     *
     * @return bool
     */
    protected function isPluginAddedInArrayMerge(FuncCall $node): bool
    {
        foreach ($node->getArgs() as $arg) {
            if (!$arg->value instanceof Array_) {
                continue;
            }

            if ($this->isPluginAdded($arg->value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \PhpParser\Node\Expr\Array_
     */
    protected function createArrayWithInstanceOf(): Array_
    {
        return new Array_(
            [$this->createArrayItemWithInstanceOf()],
        );
    }

    /**
     * @param \PhpParser\Node\Expr\Array_ $node
     *
     * @return \PhpParser\Node
     */
    protected function addNewPlugin(Array_ $node): Node
    {
        if ($this->isPluginAdded($node)) {
            return $node;
        }

        $items = [];
        $itemAdded = false;
        $pluginList = $this->getPluginList($node);
        $beforePlugin = $this->pluginPositionResolver->getFirstExistPluginByPositions(
            $pluginList,
            $this->classMetadataTransfer->getBefore()->getArrayCopy(),
        );
        $afterPlugin = $this->pluginPositionResolver->getFirstExistPluginByPositions(
            $pluginList,
            $this->classMetadataTransfer->getAfter()->getArrayCopy(),
        );
        foreach ($node->items as $item) {
            if ($item === null || !($item->value instanceof New_)) {
                $items[] = $item;

                continue;
            }

            $nodeClassName = $item->value->class->toString();
            if ($nodeClassName === $beforePlugin && !$itemAdded) {
                $items[] = $this->createArrayItemWithInstanceOf();
                $items[] = $item;
                $itemAdded = true;

                continue;
            }
            if ($nodeClassName === $afterPlugin && !$itemAdded) {
                $items[] = $item;
                $items[] = $this->createArrayItemWithInstanceOf();
                $itemAdded = true;

                continue;
            }

            $items[] = $item;
        }

        if (!$itemAdded && $beforePlugin) {
            array_unshift($items, $this->createArrayItemWithInstanceOf());
            $itemAdded = true;
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
     * @return array<string>
     */
    protected function getPluginList(Node $node): array
    {
        $plugins = [];
        foreach ($node->items as $item) {
            if ($item === null || !($item->value instanceof New_)) {
                continue;
            }

            $plugins[] = $item->value->class->toString();
        }

        return $plugins;
    }

    /**
     * @param \PhpParser\Node\Expr\Array_ $node
     *
     * @return bool
     */
    protected function isPluginAdded(Array_ $node): bool
    {
        foreach ($node->items as $item) {
            if ($this->isArrayItemEqual($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem|null $item
     *
     * @return bool
     */
    protected function isArrayItemEqual(?ArrayItem $item): bool
    {
        $classToAdd = $this->classMetadataTransfer->getSourceOrFail();

        if ($item === null || !($item->value instanceof New_)) {
            return false;
        }

        $nodeClassName = $item->value->class->toString();
        $usedParentClasses = [];
        if ($this->classLoader->classExist($nodeClassName)) {
            $usedParentClasses = $this->classLoader->loadClass($nodeClassName)->getParentClassNames();
        }

        if (
            $this->isKeyEqualsToCurrentOne($item)
            && ($nodeClassName === $classToAdd || in_array($classToAdd, $usedParentClasses))
            && $this->isEqualArguments($item)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem $item
     *
     * @return bool
     */
    protected function isEqualArguments(ArrayItem $item): bool
    {
        $value = $item->value;
        if (!($value instanceof New_)) {
            return false;
        }

        return (!$value->args && !count($this->classMetadataTransfer->getConstructorArguments())) ||
            ($value->args && $this->isArgumentEqual($value->args));
    }

    /**
     * @param array<\PhpParser\Node\Arg|\PhpParser\Node\VariadicPlaceholder> $args
     *
     * @return bool
     */
    protected function isArgumentEqual(array $args): bool
    {
        $standard = new Standard();
        $arguments = $this->classMetadataTransfer->getConstructorArguments()->getArrayCopy();
        foreach ($args as $index => $arg) {
            $argument = $arguments[$index] ?? null;
            if (!$argument) {
                continue;
            }
            $argumentValue = json_decode($argument->getValue());
            if ($standard->prettyPrintExpr($arg->value) === $argumentValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem $node
     *
     * @return bool
     */
    protected function isKeyEqualsToCurrentOne(ArrayItem $node): bool
    {
        $nodeKey = $this->getArrayItemNodeKey($node);

        return ltrim((string)$nodeKey, '\\') === ltrim((string)$this->classMetadataTransfer->getIndex(), '\\');
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem $node
     *
     * @return string|null
     */
    protected function getArrayItemNodeKey(ArrayItem $node): ?string
    {
        if ($node->key === null) {
            return null;
        }

        if ($node->key instanceof ClassConstFetch && $node->key->class instanceof Name && $node->key->name instanceof Identifier) {
            return sprintf('%s::%s', $node->key->class, $node->key->name);
        }

        if ($node->key instanceof String_) {
            return $node->key->value;
        }

        return (new Standard())->prettyPrintExpr($node->key);
    }

    /**
     * @return \PhpParser\Node\Expr\ArrayItem
     */
    protected function createArrayItemWithInstanceOf(): ArrayItem
    {
        $args = $this->argumentBuilder->createAddPluginArguments($this->classMetadataTransfer, false);

        return new ArrayItem(
            (new BuilderFactory())->new($this->classMetadataTransfer->getSourceOrFail(), $args),
            $this->indexExpr !== null ? $this->indexExpr->expr : null,
        );
    }

    /**
     * @return int
     */
    protected function successfullyProcessed(): int
    {
        $this->methodFound = false;

        return NodeTraverser::DONT_TRAVERSE_CHILDREN;
    }
}
