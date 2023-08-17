<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\PartialParser;

use PhpParser\Error;
use PhpParser\Node\Expr\BinaryOp\Div;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Parser;

class ExpressionPartialParser implements ExpressionPartialParserInterface
{
    /**
     * @var string
     */
    protected const PHP_OPEN_TAG = '<?php';

    /**
     * @var \PhpParser\Parser
     */
    protected Parser $parser;

    /**
     * @param \PhpParser\Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param string $codeString
     *
     * @return \PhpParser\Node\Stmt\Expression
     */
    public function parse(string $codeString): Expression
    {
        try {
            $ast = $this->parser->parse($this->normalizeCodeString($codeString));
        } catch (Error $e) {
            return $this->createStringExpr($codeString);
        }

        if ($ast === null) {
            return $this->createStringExpr($codeString);
        }

        if (!$this->isAstContainsExpression($ast)) {
            return $this->createStringExpr($codeString);
        }

        /** @var \PhpParser\Node\Stmt\Expression $expr */
        $expr = $ast[0];

        return $expr;
    }

    /**
     * @param string $codeString
     *
     * @return string
     */
    protected function normalizeCodeString(string $codeString): string
    {
        $codeString = strpos($codeString, static::PHP_OPEN_TAG) === false
            ? sprintf("%s\n%s", static::PHP_OPEN_TAG, $codeString)
            : $codeString;

        return rtrim(trim($codeString), ';') . ';';
    }

    /**
     * @param string $codeString
     *
     * @return \PhpParser\Node\Stmt\Expression
     */
    protected function createStringExpr(string $codeString): Expression
    {
        return new Expression(new String_($codeString));
    }

    /**
     * @param array<\PhpParser\Node\Stmt> $ast
     *
     * @return bool
     */
    protected function isAstContainsExpression(array $ast): bool
    {
        return isset($ast[0])
            && $ast[0] instanceof Expression
            && !$ast[0]->expr instanceof ConstFetch // Global constants should be considered as a strings
            && !$ast[0]->expr instanceof Div;
    }
}
