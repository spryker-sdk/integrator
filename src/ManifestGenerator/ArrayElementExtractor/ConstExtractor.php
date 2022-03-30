<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ArrayElementExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Return_;

class ConstExtractor implements ArrayElementExtractorInterface
{
    /**
     * @param \PhpParser\Node\Stmt $statement
     *
     * @return bool
     */
    public function isApplicable(Stmt $statement): bool
    {
        return $statement instanceof Return_ && $statement->expr instanceof Array_;
    }

    /**
     * @param \PhpParser\Node\Expr\Array_ $expr
     *
     * @return array<string>
     */
    public function extract(Expr $expr): array
    {
        $array = [];
        foreach ($expr->items as $item) {
            if ($item->value instanceof ClassConstFetch) {
                $array[] = sprintf('\\%s::%s', $item->value->class->toString(), $item->value->name->toString());
            }
        }

        return $array;
    }
}
