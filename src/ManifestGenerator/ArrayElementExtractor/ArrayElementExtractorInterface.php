<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ArrayElementExtractor;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

interface ArrayElementExtractorInterface
{
    /**
     * @param \PhpParser\Node\Stmt $statement
     *
     * @return bool
     */
    public function isApplicable(Stmt $statement): bool;

    /**
     * @param \PhpParser\Node\Expr $expr
     *
     * @return array
     */
    public function extract(Expr $expr): array;
}
