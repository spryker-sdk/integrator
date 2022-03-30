<?php

namespace SprykerSdk\Integrator\ManifestGenerator\PluginExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;

class VariableArrayPluginExtractor implements PluginExtractorInterface
{
    /**
     * @param \PhpParser\Node\Stmt $statement
     *
     * @return bool
     */
    public function isApplicable(Stmt $statement): bool
    {
        return $statement instanceof Expression && $statement->expr instanceof Assign && $statement->expr->expr instanceof Array_;
    }

    /**
     * @param \PhpParser\Node\Expr\Assign $expr
     *
     * @return array
     */
    public function extract(Expr $expr): array
    {
        $extractor = $this->createReturnArrayExtractor();

        return $extractor->extract($expr->expr);
    }

    /**
     * @return \SprykerSdk\Integrator\ManifestGenerator\PluginExtractor\PluginExtractorInterface
     */
    protected function createReturnArrayExtractor(): PluginExtractorInterface
    {
        return new ReturnArrayPluginExtractor();
    }
}
