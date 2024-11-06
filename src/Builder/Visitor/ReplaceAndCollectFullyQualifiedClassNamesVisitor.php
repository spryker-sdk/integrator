<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use ArrayObject;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;

class ReplaceAndCollectFullyQualifiedClassNamesVisitor extends NodeVisitorAbstract
{
    protected ArrayObject $fullyQualifiedClassNames;

    /**
     * @param \ArrayObject<string> $fullyQualifiedClassNames
     */
    public function __construct(ArrayObject $fullyQualifiedClassNames)
    {
        $this->fullyQualifiedClassNames = $fullyQualifiedClassNames;
    }

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node|int|null
     */
    public function enterNode(Node $node)
    {
        if (!($node instanceof FullyQualified)) {
            return $node;
        }

        $this->fullyQualifiedClassNames->append($node->name);

        return new Name($node->getLast());
    }

    /**
     * @return \ArrayObject<string>
     */
    public function getFullyQualifiedClassNames(): ArrayObject
    {
        return $this->fullyQualifiedClassNames;
    }
}
