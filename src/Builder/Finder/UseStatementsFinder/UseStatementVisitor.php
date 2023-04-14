<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Finder\UseStatementsFinder;

use ArrayObject;
use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeVisitorAbstract;

class UseStatementVisitor extends NodeVisitorAbstract
{
    /**
     * @var \ArrayObject
     */
    protected ArrayObject $useStatements;

    /**
     * @param \ArrayObject $useStatements
     */
    public function __construct(ArrayObject $useStatements)
    {
        $this->useStatements = $useStatements;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node
     */
    public function enterNode(Node $node): Node
    {
        if ($node instanceof Use_) {
            $this->useStatements->append($node);
        }

        return $node;
    }
}
