<?php

namespace SprykerSdk\Integrator\ManifestGenerator\PluginExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Return_;

class ReturnArrayPluginExtractor implements PluginExtractorInterface
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
     * @return array
     */
    public function extract(Expr $expr): array
    {
        $array = [];
        foreach ($expr->items as $item) {
            if ($item->value instanceof New_) {
                $array[] = '\\' . $item->value->class->toString();
            }
        }

        return $array;
    }
}
