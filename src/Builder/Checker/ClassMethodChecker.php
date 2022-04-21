<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Checker;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;

class ClassMethodChecker extends AbstractMethodChecker implements ClassMethodCheckerInterface
{
    /**
     * @var string
     */
    public const METHOD_FIELD_ARGS = 'args';

    /**
     * @var string
     */
    public const METHOD_FIELD_VAR = 'var';

    /**
     * @var string
     */
    public const METHOD_FIELD_EXPR = 'expr';

    /**
     * @var string
     */
    public const METHOD_FIELD_CLASS = 'class';

    /**
     * @var string
     */
    public const METHOD_FIELD_ITEMS = 'items';

    /**
     * @var string
     */
    public const METHOD_FIELD_NAME = 'name';

    /**
     * @var string
     */
    public const METHOD_FIELD_PARTS = 'parts';

    /**
     * @var array
     */
    protected const SINGLE_STATEMENT_EXPRESSION_FIELDS = [
        self::METHOD_FIELD_VAR,
        self::METHOD_FIELD_EXPR,
    ];

    /**
     * @var array<array-key, \SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker\MethodStatementCheckerInterface>
     */
    protected $methodStatementCheckers;

    /**
     * @var \PhpParser\ParserFactory
     */
    protected $parserFactory;

    /**
     * @param array<array-key, \SprykerSdk\Integrator\Builder\Checker\MethodStatementChecker\MethodStatementCheckerInterface> $methodStatementCheckers
     * @param \PhpParser\ParserFactory $parserFactory
     */
    public function __construct(array $methodStatementCheckers, ParserFactory $parserFactory)
    {
        $this->methodStatementCheckers = $methodStatementCheckers;
        $this->parserFactory = $parserFactory;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @return bool
     */
    public function isMethodReturnArray(ClassMethod $node): bool
    {
        if ($node->getReturnType() && $node->getReturnType() instanceof Identifier && $node->getReturnType()->name === 'array') {
            return true;
        }

        if (!$node->stmts) {
            return false;
        }

        $lastNode = end($node->stmts);

        if ($lastNode instanceof Return_ && $lastNode->expr instanceof Array_) {
            return true;
        }

        if ($lastNode instanceof Return_ && $lastNode->expr instanceof FuncCall && strpos($lastNode->expr->name->toString(), 'array_') === 0) {
            return true;
        }

        if ($lastNode instanceof Return_ && $lastNode->expr instanceof Variable) {
            $varName = $lastNode->expr->name;

            return (bool)(new NodeFinder())->findFirst($node->stmts, function (Node $node) use ($varName) {
                return $node instanceof Assign
                    && $node->var instanceof Variable
                    && $node->var->name === $varName
                    && $node->expr instanceof Array_;
            });
        }

        return false;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|null $methodNode
     * @param mixed $value
     *
     * @return bool
     */
    public function isMethodNodeSameAsValue(?ClassMethod $methodNode, $value): bool
    {
        if (!is_bool($value) && !$value) {
            return true;
        }
        if (!$methodNode || !$methodNode->stmts) {
            return true;
        }
        $parser = $this->parserFactory->create(ParserFactory::PREFER_PHP7);
        $previousValue = $parser->parse('<?php ' . $value);
        if (!$previousValue) {
            return false;
        }
        if (count($previousValue) !== count($methodNode->stmts)) {
            return false;
        }
        if (!$this->isSameMethodsBody($previousValue, $methodNode->stmts)) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed $previousValue
     * @param mixed $currentValue
     *
     * @return bool
     */
    protected function isSameMethodsBody($previousValue, $currentValue): bool
    {
        if (is_array($previousValue) && is_array($currentValue)) {
            foreach ($previousValue as $keyPreviousValue => $valuePreviousValue) {
                if (!isset($currentValue[$keyPreviousValue])) {
                    return false;
                }
                if (!$this->isSameMethodsBody($valuePreviousValue, $currentValue[$keyPreviousValue])) {
                    return false;
                }
            }

            return true;
        }

        foreach ($this->methodStatementCheckers as $methodStatementChecker) {
            if ($methodStatementChecker->isApplicable($previousValue, $currentValue)) {
                return $methodStatementChecker->isSameStatements($previousValue, $currentValue);
            }
        }

        return $this->isSameForRecursiveFields($previousValue, $currentValue);
    }

    /**
     * @param mixed $previousValue
     * @param mixed $currentValue
     *
     * @return bool
     */
    protected function isSameForRecursiveFields($previousValue, $currentValue): bool
    {
        $isSame = $this->isSameArrayStatementsFields($previousValue, $currentValue);

        return $isSame && $this->isSameSingleStatementsFields($previousValue, $currentValue);
    }

    /**
     * @param mixed $previousValue
     * @param mixed $currentValue
     *
     * @return bool
     */
    protected function isSameArrayStatementsFields($previousValue, $currentValue): bool
    {
        $isSame = true;
        if ($this->isExistsStatementsField($previousValue, $currentValue, static::METHOD_FIELD_ARGS)) {
            foreach ($previousValue->args as $keyArgument => $argument) {
                if (!$isSame) {
                    break;
                }
                $isSame = $this->isSameMethodsBody($argument->value, $currentValue->args[$keyArgument]->value);
            }
        }

        return $isSame;
    }

    /**
     * @param mixed $previousValue
     * @param mixed $currentValue
     *
     * @return bool
     */
    protected function isSameSingleStatementsFields($previousValue, $currentValue): bool
    {
        $isSame = true;
        foreach (static::SINGLE_STATEMENT_EXPRESSION_FIELDS as $field) {
            if ($isSame && $this->isExistsStatementsField($previousValue, $currentValue, $field)) {
                $isSame = $this->isSameMethodsBody($previousValue->{$field}, $currentValue->{$field});
            }
        }

        return $isSame;
    }
}
