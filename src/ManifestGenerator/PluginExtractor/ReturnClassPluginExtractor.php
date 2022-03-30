<?php

namespace SprykerSdk\Integrator\ManifestGenerator\PluginExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Return_;

class ReturnClassPluginExtractor implements PluginExtractorInterface
{
    /**
     * @param \PhpParser\Node\Stmt $statement
     *
     * @return bool
     */
    public function isApplicable(Stmt $statement): bool
    {
        return $statement instanceof Return_ && $statement->expr instanceof New_;
    }

    /**
     * @param \PhpParser\Node\Expr\New_ $expr
     *
     * @return array
     */
    public function extract(Expr $expr): array
    {
        return ['\\' . $expr->class->toString()];
    }
}
